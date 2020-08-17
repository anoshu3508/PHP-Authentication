<?php
// Import the necessary classes
use Cartalyst\Sentinel\Native\Facades\Sentinel;

// ログアウト処理
if ($USER_INFO !== null) {
    Sentinel::logout($USER_INFO);
    $USER_INFO = null;
}

$smarty->assign('PAGE_TITLE', "ログイン");
$smarty->assign('CSS_FILE_NAME', "login");
// $smarty->assign('JS_FILE_NAME', "login");
$smarty->assign('errorFlg', false);
$smarty->assign('MAIN_HTML', $smarty->fetch('login/login.tpl'));