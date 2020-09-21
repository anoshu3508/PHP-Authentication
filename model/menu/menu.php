<?php
use Josantonius\Session\Session;

// セッション全削除
Session::destroy('', true);

$smarty->assign('PAGE_TITLE', "メニュー");
$smarty->assign('CSS_FILE_NAME', "menu");
$smarty->assign('JS_FILE_NAME', "menu");
$smarty->assign('MAIN_HTML', $smarty->fetch('menu/menu.tpl'));