<?php
// 必要なクラスの読み込み
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;

// データベース接続に必要な設定値を取得
if ($_SERVER['SERVER_NAME'] === 'activezero.co.jp') {
    $dbconf = parse_ini_file(CONFIG . 'database.ini', true)['product'];
} else {
    $dbconf = parse_ini_file(CONFIG . 'database.ini', true)['develop'];
}

// データベース接続
$capsule = new Capsule();
$capsule->addConnection($dbconf);
$capsule->bootEloquent();

// TODO 管理者機能
$adminFlg = false;
if (isset($_GET['adminkey'])) {
    $adminkey = filter_input(INPUT_GET, 'adminkey');
    setcookie('adminkey', $adminkey);
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

// ----------------------------------------------------------------------------
// ■ POSTリクエストの場合
// ----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // アクション名を取得
    $action = filter_input(INPUT_POST, 'action');

    // TODO 管理者機能
    if ($adminFlg) {
        switch ($action) {
            case 'userRegist':
                require_once MODEL . 'admin/userRegist.php';
                break;
            default:
                require_once MODEL . 'error.php';
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
                /****************************************
                 * ユーザ登録画面
                 ****************************************/ 
                case 'userRegistInput':
                    require_once MODEL . 'userRegist/input.php';
                    break;
                case 'userRegistConfirm':
                    require_once MODEL . 'userRegist/confirm.php';
                    break;
                case 'userRegistComplete':
                    require_once MODEL . 'userRegist/complete.php';
                    break;
                default:
                    require_once MODEL . 'login/login.php';
            }
        }
    }

// ----------------------------------------------------------------------------
// ■ GETリクエストの場合
// ----------------------------------------------------------------------------
} else {
    // TODO 管理者機能
    if ($adminFlg) {
        $action = filter_input(INPUT_GET, 'action');
        switch ($action) {
            case 'userRegist':
                require_once MODEL . 'admin/userRegist.php';
                break;
            default:
                require_once MODEL . 'login/login.php';
        }
    } else {
        // ログイン画面を表示
        require_once MODEL . 'login/login.php';
    }
}

$smarty->display('common.tpl');