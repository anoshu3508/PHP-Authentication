<?php
$smarty->assign('PAGE_TITLE', "ファイル共有");
$smarty->assign('CSS_FILE_NAME', "file_sharing");
$smarty->assign('JS_FILE_NAME', "file_sharing");
$smarty->assign('MAIN_HTML', $smarty->fetch('fileSharing/file_sharing.tpl'));