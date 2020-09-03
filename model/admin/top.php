<?php
// Import the necessary classes
use Cartalyst\Sentinel\Native\Facades\Sentinel;

// ログイン済の場合は、ログアウト処理を実行
if ($USER_INFO !== null) {
    Sentinel::logout($USER_INFO);
    $USER_INFO = null;
}

$smarty->assign('PAGE_TITLE', "管理者トップページ");
$smarty->assign('CSS_FILE_NAME', "admin");
// $smarty->assign('JS_FILE_NAME', "admin_top");
$smarty->assign('MAIN_HTML', $smarty->fetch('admin/top.tpl'));