<?php
// Include the composer autoload file
require '../vendor/autoload.php';

// Import the necessary classes
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;
use Verot\Upload\Upload;
use League\Csv\Reader;
use League\Csv\CharsetConverter;

/************************************************
 * ユーザ登録CSV画面 共通部（最初）
 ***********************************************/
echo '
    <!DOCTYPE html>
    <html lang="ja">
        <head>
            <title>ユーザ登録CSV | アクティブゼロ管理画面</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width,initial-scale=1.0">
            <link rel="shortcut icon" type="image/vnd.microsoft.icon" sizes="16x16" href="/admin/favicon.ico" />
        </head>
        <body>
';

// ------------------------------------------------------------------------
// ■ POSTリクエストの場合
// ------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ------------------------------------------
    // ファイルアップロード 処理開始
    // ------------------------------------------
    // アップロードファイルを取得
    $csvFile = $_FILES['csv_file'];

    // ファイルハンドラーを生成
    $handle = new Upload($_FILES['csv_file'], 'ja_JP');

    // アップロードの設定
    $handle->dir_chmod = 0755;       // ディレクトリが書き込めない場合に変更する属性
    $handle->allowed = [             // 許可するMIMEタイプ
        'text/plain',
        'text/csv'
    ];
    $handle->no_script = false;      // テキストファイルに変換するか

    // 格納先ディレクトリを取得
    $csvDir = '../work/userRegist/' . date('Y') . '/' . date('m');

    // アップロードファイルのチェック
    if ($handle->uploaded) {
        // 格納先ディレクトリを指定して保存
        $handle->process($csvDir);
        if ($handle->processed) {
            // アップロード成功
            // echo 'アップロード成功<br/>';
        } else {
            // アップロード処理失敗
            echo $handle->error;
        }
    } else {
        // アップロード失敗
        echo $handle->error;
    }
    // ------------------------------------------
    // ファイルアップロード 処理終了
    // ------------------------------------------

    // ------------------------------------------
    // DB接続 処理開始
    // ------------------------------------------
    $capsule = new Capsule();

    $capsule->addConnection([
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'database'  => 'activezero_co_jp',
        'username'  => 'activezero',
        'password'  => 'Activezero1101',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ]);

    $capsule->bootEloquent();
    // ------------------------------------------
    // DB接続 処理終了
    // ------------------------------------------

    // ------------------------------------------
    // CSVファイル読み込み 処理開始
    // ------------------------------------------
    // CSVファイルを指定
    $csvFile = Reader::createFromPath($handle->file_dst_pathname, 'r');

    // 文字エンコードを指定(SJIS-win -> UTF-8)
    CharsetConverter::addTo($csvFile, 'SJIS-win', 'UTF-8');

    // ヘッダーを設定
    $csvFile->setHeaderOffset(0);

    // ヘッダーとレコードを読み込み
    $csvHeader = $csvFile->getHeader();
    $csvRecords = $csvFile->getRecords();

    // ヘッダーがない場合は処理終了
    if ($csvHeader[0] !== 'email' || $csvHeader[1] !== 'password') {
        $csvErrorFlg = true;
        echo '1行目にヘッダーを入力してください。（1列目=email, 2列目=password）<br/>';
        exit(1);
    }

    // バリデーションを実行
    $csvErrorFlg = false;
    foreach ($csvRecords as $idx => $row) {
        // メールアドレスの未入力チェック
        if (!isset($row['email']) || $row['email'] === '') {
            $csvErrorFlg = true;
            echo 'メールアドレスが入力されていません。（行番号:' . ($idx + 1) . '）<br/>';
            continue;
        }
        // パスワードの未入力チェック
        if (!isset($row['password']) || $row['password'] === '') {
            $csvErrorFlg = true;
            echo 'パスワードが入力されていません。（行番号:' . ($idx + 1) . '）<br/>';
            continue;
        }
    
        // メールアドレスの形式チェック
        $emailPattern = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/";
        if (!preg_match($emailPattern, $row['email'])) {
            $csvErrorFlg = true;
            echo 'メールアドレスの形式が不正です（行番号:' . ($idx + 1) . ', 値:' . $row['email'] . '）<br/>';
        } else {
            // アクティブゼロドメイン以外のメールアドレスは許容しない
            $azDomainPattern = "/@activezero.co.jp$/";
            if (!preg_match($azDomainPattern, $row['email'])) {
                $csvErrorFlg = true;
                echo 'AZ以外のメールアドレスは登録できません。（行番号:' . ($idx + 1) . ', 値:' . $row['email'] . '）<br/>';
            }
        }
    
        // パスワードの形式チェック
        $pwPattern = '/^[!-~]{8,100}+$/';
        if (!preg_match($pwPattern, $row['password'])) {
            $csvErrorFlg = true;
            echo 'パスワードは8～100文字の半角英数記号が使用できます。（行番号:' . ($idx + 1) . ', 値:' . $row['password'] . '）<br/>';
        }

    }

    // エラーがある場合は処理終了
    if ($csvErrorFlg) {
        exit(1);
    }
    // ------------------------------------------
    // CSVファイル読み込み 処理終了
    // ------------------------------------------

    // ------------------------------------------
    // ユーザ登録 処理開始
    // ------------------------------------------
    $newUserCount = 0;
    $updateUserCount = 0;
    foreach ($csvRecords as $idx => $row) {
        $credentials = [
            'email' => $row['email'],
            'password' => $row['password']
        ];

        // 登録済みかを確認
        $userObj = Sentinel::getUserRepository();
        $user = $userObj->findByCredentials($credentials);
        if (!is_null($user)) {
            // 存在する場合は更新
            $updateUserCount++;
            $user = $userObj->update($user, $credentials);
        } else { 
            // 存在しない場合は、新規登録
            $newUserCount++;
            $user = Sentinel::registerAndActivate($credentials);
        }
    }

    echo '登録が完了しました。<br/>';
    echo '新規登録：' . $newUserCount . '件、更新：' . $updateUserCount . '件';
    // ------------------------------------------
    // アカウント登録 処理終了
    // ------------------------------------------

// ------------------------------------------------------------------------
// ■ GETリクエストの場合
// ------------------------------------------------------------------------
} else {
    echo '
            <form action="/admin/userRegist.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="csv_file" accept=".csv" />
                <input type="submit" value="送信" />
            </form>
    ';
}

/************************************************
 * ユーザ登録CSV画面 共通部（最後）
 ***********************************************/
echo '
        </body>
    </html>
';

// 一覧
/*
$userObj = Sentinel::getUserRepository();
$roleObj = Sentinel::getRoleRepository();
$persistenceObj = Sentinel::getPersistenceRepository();
$activationObj = Sentinel::getActivationRepository();
$reminderObj = Sentinel::getReminderRepository();
$throttleObj = Sentinel::getThrottleRepository();
*/