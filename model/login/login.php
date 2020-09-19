<?php
// Import the necessary classes
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Josantonius\Session\Session;

// ログイン済の場合は、ログアウト処理を実行
if ($USER_INFO !== null) {
    Sentinel::logout($USER_INFO);
    $USER_INFO = null;
}

// セッション全削除
Session::destroy('', true);

$smarty->assign('PAGE_TITLE', "ログイン");
$smarty->assign('CSS_FILE_NAME', "login");
// $smarty->assign('JS_FILE_NAME', "login");
$smarty->assign('errorFlg', false);
$smarty->assign('MAIN_HTML', $smarty->fetch('login/login.tpl'));