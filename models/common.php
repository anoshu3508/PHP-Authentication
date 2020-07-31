<?php
// Import the necessary classes
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;

// Setup a new Eloquent Capsule instance
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

// ログイン状態をチェック
$USER_INFO = null;
if (Sentinel::check()) {
    // ログインユーザ情報を取得
    $USER_INFO = Sentinel::getUser();
}

// ------------------------------------------------------------------------
// ■ POSTリクエストの場合
// ------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // アクション名を取得
    $action = filter_input(INPUT_POST, 'action');

    // ------------------------------------------------------------------------
    // ログイン済の場合
    // ------------------------------------------------------------------------
    if ($USER_INFO !== null) {
        // アクションを実行
        switch ($action) {
            /****************************************
             * ログイン画面
             ****************************************/ 
            case 'login':
                require_once 'login/login.php';
                break;
            case 'authentication':
                require_once 'login/authentication.php';
                break;
            /****************************************
             * エラー画面
             ****************************************/ 
            default:
                require_once 'error.php';
        }
    // ------------------------------------------------------------------------
    // 未ログインの場合
    // ------------------------------------------------------------------------
    } else {
        // アクションを実行
        switch ($action) {
            /****************************************
             * ログイン画面（認証処理）
             ****************************************/ 
            case 'authentication':
                require_once 'login/authentication.php';
                break;
            /****************************************
             * ユーザ登録画面
             ****************************************/ 
            case 'userRegistInput':
                require_once 'userRegist/input.php';
                break;
            case 'userRegistConfirm':
                require_once 'userRegist/confirm.php';
                break;
            case 'userRegistComplete':
                require_once 'userRegist/complete.php';
                break;
            default:
                require_once 'login/login.php';
        }
    }

// ------------------------------------------------------------------------
// ■ GETリクエストの場合
// ------------------------------------------------------------------------
} else {
    // ログイン画面を表示
    require_once 'login/login.php';
}

$smarty->display('common.tpl');