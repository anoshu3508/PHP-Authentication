<?php
// Import the necessary classes
use Cartalyst\Sentinel\Native\Facades\Sentinel;

$credentials = [
    'email' => filter_input(INPUT_POST, 'email'),
    'password' => filter_input(INPUT_POST, 'password')
];

// 認証処理
$user = Sentinel::authenticate($credentials);

// 認証に成功した場合
if ($user !== false) {
    // ログインユーザ情報を取得
    $USER_INFO = $user;

    $smarty->assign('PAGE_TITLE', "メニュー");
    $smarty->assign('CSS_FILE_NAME', "menu");
    $smarty->assign('JS_FILE_NAME', "menu");
    $mainTemplate = $smarty->fetch('menu/menu.tpl');

// 認証に失敗した場合
} else {
    $smarty->assign('PAGE_TITLE', "ログイン");
    $smarty->assign('CSS_FILE_NAME', "login");
    $smarty->assign('errorFlg', true);
    $mainTemplate = $smarty->fetch('login/login.tpl');
}

$smarty->assign('MAIN_HTML', $mainTemplate);
