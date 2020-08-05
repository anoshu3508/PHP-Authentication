<?php
// Import the necessary classes
use Cartalyst\Sentinel\Native\Facades\Sentinel;

// ログイン済の場合は、ログアウト処理を実行
if ($USER_INFO !== null) {
    Sentinel::logout($USER_INFO);
}

$smarty->assign('PAGE_TITLE', "ログイン");
$smarty->assign('CSS_FILE_NAME', "login");
// $smarty->assign('JS_FILE_NAME', "login");
$smarty->assign('errorFlg', false);
$smarty->assign('MAIN_HTML', $smarty->fetch('login/login.tpl'));