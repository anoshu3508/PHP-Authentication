<?php
// 必要なクラスの読み込み
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;
use EasyCSRF\NativeSessionProvider;
use EasyCSRF\EasyCSRF;
use Josantonius\Session\Session;

// セッション開始
Session::init();

// データベース接続に必要な設定値を取得
if (strpos($_SERVER['SERVER_NAME'], 'activezero.co.jp') !== false) {
    $dbconf = parse_ini_file(CONFIG . 'database.ini', true)['product'];
} else {
    $dbconf = parse_ini_file(CONFIG . 'database.ini', true)['develop'];
}

// 通常のデータベース接続
$connString  = $dbconf['driver'] . ':';
$connString .= 'host=' . $dbconf['host'] . ';';
$connString .= 'dbname=' . $dbconf['database'] . ';';
$connString .= 'charset=' . $dbconf['charset'];
ORM::configure([
    'connection_string' => $connString,
    'username'          => $dbconf['username'],
    'password'          => $dbconf['password'],
]);

// 認証用のデータベース接続
$capsule = new Capsule();
$capsule->addConnection($dbconf);
$capsule->bootEloquent();

// TODO 管理者機能
$adminFlg = false;
if (isset($_GET['adminkey'])) {
    $adminkey = filter_input(INPUT_GET, 'adminkey');
    setcookie('adminkey', $adminkey, time()+3600);
} else {
    $adminkey = filter_input(INPUT_COOKIE, 'adminkey');
}
if (isset($adminkey)) {
    $adminkeyConf = parse_ini_file(CONFIG . 'adminkey.ini')['key'];
    if ($adminkey === $adminkeyConf) {
        $adminFlg = true;
    }
}

// ログイン状態をチェック
$USER_INFO = null;
if (Sentinel::check()) {
    // ログインユーザ情報を取得
    $USER_INFO = Sentinel::getUser();
}

// CSRFトークンの発行準備（描画直前に発行）
$sessionProvider = new NativeSessionProvider();
$easyCSRF = new EasyCSRF($sessionProvider);

// ----------------------------------------------------------------------------
// ■ POSTリクエストの場合
// ----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // トークンチェック
    $IS_VALID_TOKEN = true;
    try {
        $easyCSRF->check('my_token', filter_input(INPUT_POST, 'csrf_token'));
    } catch(Exception $e) {
        $IS_VALID_TOKEN = false;
    }

    // トークンを発行
    $CSRF_TOKEN = $easyCSRF->generate('my_token');
    $smarty->assign('CSRF_TOKEN', $CSRF_TOKEN);

    // アクション名を取得
    $action = filter_input(INPUT_POST, 'action');

    // TODO 管理者機能
    if ($adminFlg) {
        switch ($action) {
            /****************************************
             * ユーザ登録CSV（管理者機能）
             ****************************************/
            case 'userRegist':
                require_once MODEL . 'admin/userRegist.php';
                break;
            default:
                require_once MODEL . 'admin/error.php';
        }
    } else {
        // ----------------------------------------------------------
        // ログイン済の場合
        // ----------------------------------------------------------
        if ($USER_INFO !== null) {
            // アクションを実行
            switch ($action) {
                /****************************************
                 * ログイン画面
                 ****************************************/
                case 'login':
                    require_once MODEL . 'login/login.php';
                    break;
                case 'authentication':
                    require_once MODEL . 'login/authentication.php';
                    break;
                /****************************************
                 * メニュー画面
                 ****************************************/
                case 'menu':
                    require_once MODEL . 'menu/menu.php';
                    break;
                /****************************************
                 * ファイル共有画面
                 ****************************************/
                case 'fileSharing':
                    require_once MODEL . 'fileSharing/fileSharing.php';
                    break;
                case 'uploadShareFile':
                    require_once MODEL . 'fileSharing/uploadShareFile.php';
                    break;
                case 'downloadShareFile':
                    require_once MODEL . 'fileSharing/downloadShareFile.php';
                    break;
                case 'deleteShareFile':
                    require_once MODEL . 'fileSharing/deleteShareFile.php';
                    break;
                /****************************************
                 * 作業報告書画面
                 ****************************************/
                case 'workReport':
                    require_once MODEL . 'workReport/workReport.php';
                    break;
                case 'registWorkReport':
                    require_once MODEL . 'workReport/registWorkReport.php';
                    break;
                case 'confirmWorkReport':
                    require_once MODEL . 'workReport/confirmWorkReport.php';
                    break;
                case 'completeWorkReport':
                    require_once MODEL . 'workReport/completeWorkReport.php';
                    break;
                case 'outputWorkReport':
                    require_once MODEL . 'workReport/outputWorkReport.php';
                    break;
                /****************************************
                 * ログアウト
                 ****************************************/
                case 'logout':
                    require_once MODEL . 'header/logout.php';
                    break;
                /****************************************
                 * エラー画面
                 ****************************************/
                default:
                    require_once MODEL . 'error.php';
            }
        // ----------------------------------------------------------
        // 未ログインの場合
        // ----------------------------------------------------------
        } else {
            // アクションを実行
            switch ($action) {
                /****************************************
                 * ログイン画面（認証処理）
                 ****************************************/
                case 'authentication':
                    require_once MODEL . 'login/authentication.php';
                    break;
                default:
                    require_once MODEL . 'login/login.php';
            }
        }
    }

    // アクション名をセッションに格納
    Session::set('before_action', $action);

// ----------------------------------------------------------------------------
// ■ GETリクエストの場合
// ----------------------------------------------------------------------------
} else {
    // トークンを発行
    $CSRF_TOKEN = $easyCSRF->generate('my_token');
    $smarty->assign('CSRF_TOKEN', $CSRF_TOKEN);

    // TODO 管理者機能
    if ($adminFlg) {
        $action = filter_input(INPUT_GET, 'action');
        switch ($action) {
            case 'userRegist':
                require_once MODEL . 'admin/userRegist.php';
                break;
            case 'exit':
                require_once MODEL . 'admin/exit.php';
            default:
                require_once MODEL . 'admin/top.php';
        }
    } else {
        // ログイン画面を表示
        require_once MODEL . 'login/login.php';
    }
}

$smarty->assign('USER_INFO', $USER_INFO);
$smarty->display('common.tpl');