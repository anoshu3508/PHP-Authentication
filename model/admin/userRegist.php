<?php
// Import the necessary classes
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;
use Verot\Upload\Upload;
use League\Csv\Reader;
use League\Csv\CharsetConverter;

// ------------------------------------------------------------------------
// ■ POSTリクエストの場合
// ------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // メッセージを初期化
    $message = '';

    try {
        // ファイルアップロード
        $filePath = uploadUserRegistCsvFile($_FILES['csv_file']);
        // CSVファイル読み込み
        $csvRecords = readUserRegistCsvFile($filePath);

        // ユーザの登録・更新・削除
        $newUserCount = 0;
        $updateUserCount = 0;
        $deleteUserCount = 0;
        $skipUserCount = 0;
        foreach ($csvRecords as $idx => $row) {
            // ユーザ情報を取得
            $userInfo['email'] = $row['email'];
            if (!empty($row['password'])) {
                $userInfo['password'] = $row['password'];
            }
            if (!empty($row['last_name'])) {
                $userInfo['last_name'] = $row['last_name'];
            }
            if (!empty($row['first_name'])) {
                $userInfo['first_name'] = $row['first_name'];
            }

            // 登録済みかを確認
            $userObj = Sentinel::getUserRepository();
            $user = $userObj->findByCredentials([
                'login' => $userInfo['email']
            ]);

            // 処理区分別に処理
            switch ($row['category']) {
                // 新規
                case 'ADD':
                    if ($user !== null) {
                        // 存在する場合はスキップ
                        $skipUserCount++;
                        $message .= 'このメールアドレスは既に使用されているため、登録処理をスキップしました。';
                        $message .= '（行番号:' . ($idx + 1) . ', 値:' . $row['email'] . '）<br/>';
                    } else {
                        // 存在しない場合は新規登録
                        $newUserCount++;
                        $user = Sentinel::registerAndActivate($userInfo);
                    }
                    break;
                // 更新
                case 'EDIT':
                    if ($user !== null) {
                        // 存在する場合は更新
                        $updateUserCount++;
                        $user = $userObj->update($user, $userInfo);
                    } else {
                        // 存在しない場合はスキップ
                        $skipUserCount++;
                        $message .= 'このメールアドレスは未使用のため、更新処理をスキップしました。';
                        $message .= '（行番号:' . ($idx + 1) . ', 値:' . $row['email'] . '）<br/>';
                    }
                    break;
                // 削除
                case 'DELETE':
                    if ($user !== null) {
                        // 存在する場合は削除
                        $deleteUserCount++;
                        $user->delete();
                    } else {
                        // 存在しない場合はスキップ
                        $skipUserCount++;
                        $message .= 'このメールアドレスは未使用のため、削除処理をスキップしました。';
                        $message .= '（行番号:' . ($idx + 1) . ', 値:' . $row['email'] . '）<br/>';
                    }
                    break;
                default:
                    $skipUserCount++;
                    $message .= 'このメッセージが表示された場合は、開発者に連絡してください。';
                    $message .= '（行番号:' . ($idx + 1) . ', 値:' . $row['category'] . '）<br/>';
            }
        }

        if (!empty($message)) {
            $message .= '<br/>';
        }
        $message .= '処理が完了しました。<br/>';
        if ($newUserCount > 0) {
            $message .= '・新規：' . $newUserCount . '件<br/>';
        }
        if ($updateUserCount > 0) {
            $message .= '・更新：' . $updateUserCount . '件<br/>';
        }
        if ($deleteUserCount > 0) {
            $message .= '・削除：' . $deleteUserCount . '件<br/>';
        }
        if ($skipUserCount > 0) {
            $message .= '・スキップ：' . $skipUserCount . '件<br/>';
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
    }

    // メッセージを画面に追加
    $smarty->assign(compact('message'));
}

$smarty->assign('PAGE_TITLE', "ユーザ登録CSV");
$smarty->assign('CSS_FILE_NAME', "admin");
$smarty->assign('MAIN_HTML', $smarty->fetch('admin/user_regist.tpl'));

// Sentinel リポジトリ一覧
/*
$userObj = Sentinel::getUserRepository();
$roleObj = Sentinel::getRoleRepository();
$persistenceObj = Sentinel::getPersistenceRepository();
$activationObj = Sentinel::getActivationRepository();
$reminderObj = Sentinel::getReminderRepository();
$throttleObj = Sentinel::getThrottleRepository();
*/

/**
 * CSVファイルアップロード
 *
 * @param $csvFile CSVファイル
 * @return アップロードファイルのフルパス
 */
function uploadUserRegistCsvFile($csvFile) {
    // ファイルハンドラーを生成
    $handle = new Upload($csvFile, 'ja_JP');

    // アップロードの設定
    $handle->dir_chmod = 0755;       // ディレクトリが書き込めない場合に変更する属性
    $handle->allowed = [             // 許可するMIMEタイプ
        'text/plain',
        'text/csv'
    ];
    $handle->no_script = false;      // テキストファイルに変換するか

    // 格納先ディレクトリを取得
    $directory = WORK . 'userRegist' . DS . date('Y') . '/' . date('m');

    // アップロードファイルのチェック
    if ($handle->uploaded) {
        // 格納先ディレクトリを指定して保存
        $handle->process($directory);
        if ($handle->processed) {
            // アップロード成功
        } else {
            // アップロード処理失敗
            throw new Exception($handle->error);
        }
    } else {
        // アップロード失敗
        throw new Exception($handle->error);
    }

    return $handle->file_dst_pathname;
}

/**
 * CSVファイル読み込み
 *
 * @param $filePath ファイルパス
 * @return CSVレコード
 */
function readUserRegistCsvFile($filePath) {
    // CSVファイルを指定
    $csvFile = Reader::createFromPath($filePath, 'r');

    // 文字エンコードを指定(SJIS-win -> UTF-8)
    CharsetConverter::addTo($csvFile, 'SJIS-win', 'UTF-8');

    // ヘッダーを設定
    $csvFile->setHeaderOffset(0);

    // ヘッダーとレコードを読み込み
    $csvHeader = $csvFile->getHeader();
    $csvRecords = $csvFile->getRecords();

    $errorMsg = '';

    // ヘッダーがない場合は例外をスロー
    if (
        $csvHeader[0] !== 'category'
        || (!isset($csvHeader[1]) || $csvHeader[1] !== 'email')
        || (!isset($csvHeader[2]) || $csvHeader[2] !== 'password')
        || (!isset($csvHeader[3]) || $csvHeader[3] !== 'last_name')
        || (!isset($csvHeader[4]) || $csvHeader[4] !== 'first_name')
    ) {
        $errorMsg .= '1行目にヘッダーを入力してください。（1列目=category, 2列目=email, 3列目=password, 4列目=last_name, 3列目=first_name）<br/>';
        throw new Exception($errorMsg);
    }

    // バリデーションを実行し、エラーがある場合は例外をスロー
    if (!validateUserRegistCsvFile($csvRecords, $errorMsg)) {
        throw new Exception($errorMsg);
    }

    return $csvRecords;
}

/**
 * CSVファイルのバリデーション
 *
 * @param $csvRecords ファイルパス
 * @param &$errorMsg エラーメッセージ（参照渡し）
 * @return バリデーション結果
 */
function validateUserRegistCsvFile($csvRecords, &$errorMsg) {
    $csvValidFlg = true;

    // レコード毎に処理
    foreach ($csvRecords as $idx => $row) {
        // 処理区分の未入力チェック
        if (empty($row['category'])) {
            $csvValidFlg = false;
            $errorMsg .= '処理区分が入力されていません。（行番号:' . ($idx + 1) . '）<br/>';
            continue;
        }
        // メールアドレスの未入力チェック
        if (empty($row['email'])) {
            $csvValidFlg = false;
            $errorMsg .= 'メールアドレスが入力されていません。（行番号:' . ($idx + 1) . '）<br/>';
            continue;
        }

        // 新規の場合のチェック
        if ($row['category'] === 'ADD') {
            // パスワードの未入力チェック（新規のみ必須）
            if (empty($row['password'])) {
                $csvValidFlg = false;
                $errorMsg .= 'パスワードが入力されていません。（行番号:' . ($idx + 1) . '）<br/>';
                continue;
            }
            // 姓の未入力チェック（新規のみ必須）
            if (empty($row['last_name'])) {
                $csvValidFlg = false;
                $errorMsg .= '名が入力されていません。（行番号:' . ($idx + 1) . '）<br/>';
                continue;
            }
            // 名の未入力チェック（新規のみ必須）
            if (empty($row['first_name'])) {
                $csvValidFlg = false;
                $errorMsg .= '姓が入力されていません。（行番号:' . ($idx + 1) . '）<br/>';
                continue;
            }
        }

        // 処理区分の形式チェック
        $catPattern = "/^ADD|EDIT|DELETE$/";
        if (!preg_match($catPattern, $row['category'])) {
            $csvValidFlg = false;
            $errorMsg .= '処理区分は ADD, EDIT, DELETE のいずれかを使用して下さい。（行番号:' . ($idx + 1) . ', 値:' . $row['password'] . '）<br/>';
        }

        // メールアドレスの形式チェック
        $emailPattern = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/";
        if (!preg_match($emailPattern, $row['email'])) {
            $csvValidFlg = false;
            $errorMsg .= 'メールアドレスの形式が不正です（行番号:' . ($idx + 1) . ', 値:' . $row['email'] . '）<br/>';
        } else {
            // アクティブゼロドメイン以外のメールアドレスは許容しない
            $azDomainPattern = "/@activezero.co.jp$/";
            if (!preg_match($azDomainPattern, $row['email'])) {
                $csvValidFlg = false;
                $errorMsg .= 'AZ以外のメールアドレスは登録できません。（行番号:' . ($idx + 1) . ', 値:' . $row['email'] . '）<br/>';
            }
        }

        // パスワードの形式チェック（削除の場合は不要）
        $pwPattern = '/^[!-~]{8,100}+$/';
        if (
            $row['category'] !== 'DELETE'
            && !empty($row['password'])
            && !preg_match($pwPattern, $row['password'])
        ) {
            $csvValidFlg = false;
            $errorMsg .= 'パスワードは8～100文字の半角英数記号を使用して下さい。（行番号:' . ($idx + 1) . ', 値:' . $row['password'] . '）<br/>';
        }
    }

    return $csvValidFlg;
}