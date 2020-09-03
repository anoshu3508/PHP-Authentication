<?php
$smarty->assign('PAGE_TITLE', "作業時間入力");
$smarty->assign('CSS_FILE_NAME', "work_time");
$smarty->assign('JS_FILE_NAME', "work_time");
$smarty->assign('MAIN_HTML', $smarty->fetch('workTime/work_time.tpl'));