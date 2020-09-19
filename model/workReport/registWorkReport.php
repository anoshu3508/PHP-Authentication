<?php
use Josantonius\Session\Session;

// 作業者名を取得
$worker = $USER_INFO->last_name . ' ' . $USER_INFO->first_name;

// 勤務フラグ一覧を取得
$workFlagSelectList = [
    '0' => '平日通常出社',
    '1' => '全日休暇',
    '2' => '午前半休',
    '3' => '午後半休',
    '4' => '振替休暇',
    '5' => '振替出勤',
    '6' => '休日出勤',
    '9' => '休日',
];

// 日付のデフォルト値を取得
$yyyymm = filter_input(INPUT_POST, 'yyyymm');
$nowYm = date('Y/m');
$date = date('Y/m/d');
if ($yyyymm !== $nowYm) {
    $date = $yyyymm . '/01';
}

// 作業報告情報のデフォルト値を格納
$workReportDefault = [
    'customer' => '',
    'work_flag' => '0',
    'date' => $date,
    'start_time' => '',
    'end_time' => '',
    'break_hours' => '',
    'operation_hours' => '',
    'overtime_hours' => '',
    'holiday_hours' => '',
    'midnight_hours' => '',
    'work_description' => '',
];

// セッションから作業報告情報を取得
$workReport = Session::pull('workReport') ?? [];
$workReport += $workReportDefault;

$smarty->assign('PAGE_TITLE', "作業報告登録");
$smarty->assign('CSS_FILE_NAME', "workReport/regist");
$smarty->assign('JS_FILE_NAME', "workReport/regist");
$smarty->assign(compact('worker', 'workReport', 'workFlagSelectList'));
$smarty->assign('MAIN_HTML', $smarty->fetch('workReport/regist.tpl'));