<?php
use Josantonius\Session\Session;

// 作業者名を取得
$worker = $USER_INFO->last_name . ' ' . $USER_INFO->first_name;

// TODO [マスタ化] 勤務フラグ一覧（すべて）を取得
$workFlagSelectAllList = [
    '0' => '平日通常出社',
    '1' => '全日休暇',
    '2' => '午前半休',
    '3' => '午後半休',
    '4' => '振替休暇',
    '5' => '振替出勤',
    '6' => '休日出勤',
    '9' => '休日',
];

// バリデーション実行（エラーの場合は例外にスロー）
$postData = [
    'yyyymm' => filter_input(INPUT_POST, 'yyyymm'),
    'day' => filter_input(INPUT_POST, 'day'),
];
$postData = validateWorkReport($postData);

// 日付のデフォルト値を取得
$nowYm = date('Y/m');
$date = date('Y/m/d');
if ($postData['yyyymm'] === $nowYm) {
    $date = $postData['yyyymm'] . '/' . $postData['day'];
} else {
    $date = $postData['yyyymm'] . '/01';
}

// 月別の作業報告情報を取得
$workReportMonthly = ORM::for_table('work_report_monthly')
    ->where('user_id', $USER_INFO->id)
    ->where('yyyymm', str_replace('/', '', $postData['yyyymm']))
    ->find_one();
if (!$workReportMonthly) {
    $workReportMonthly = ORM::for_table('work_report_monthly')
        ->where('user_id', $USER_INFO->id)
        ->order_by_desc('yyyymm')
        ->find_one();
}

// 日別の作業報告情報を取得
$workReportDaily = ORM::for_table('work_report_daily')
    ->select_many_expr([
        'date' => 'DATE_FORMAT(date, "%Y/%m/%d")',
        'start_time' => 'TIME_FORMAT(start_time, "%H:%i")',
        'end_time' => 'TIME_FORMAT(end_time, "%H:%i")',
    ])
    ->select_many([
        'work_flag',
        'break_hours',
        'operation_hours',
        'overtime_hours',
        'holiday_hours',
        'midnight_hours',
        'work_description'
    ])
    ->where([
        'user_id' => $USER_INFO->id,
        'date' => $date
    ])
    ->order_by_asc('date')
    ->find_one();

// 祝日マスタから今日の祝日情報を取得
$holidayMstDay = ORM::for_table('holiday_mst')
    ->where('date', $date)
    ->find_one();

// 曜日を取得
$dayOfWeek = date('w', strtotime(str_replace('/', '-', $date)));

// 祝日用の勤務フラグを取得
if ($holidayMstDay || $dayOfWeek === '0' || $dayOfWeek === '6') {
    $workFlag = '9';
    $workFlagSelectList = array_slice($workFlagSelectAllList, 5, null, true);
// 平日用の勤務フラグを取得
} else {
    $workFlag = '0';
    $workFlagSelectList = array_slice($workFlagSelectAllList, 0, 5, true);
}

// 作業報告情報のデフォルト値を格納
$workReportDefault = [
    'customer' => $workReportMonthly->customer ?? '',
    'work_flag' => $workReportDaily->work_flag ?? $workFlag,
    'date' => $date,
    'start_time' => $workReportDaily->start_time ?? '',
    'end_time' => $workReportDaily->end_time ?? '',
    'break_hours' => $workReportDaily->break_hours ?? '',
    'operation_hours' => $workReportDaily->operation_hours ?? '',
    'overtime_hours' => $workReportDaily->overtime_hours ?? '',
    'holiday_hours' => $workReportDaily->holiday_hours ?? '',
    'midnight_hours' => $workReportDaily->midnight_hours ?? '',
    'work_description' => $workReportDaily->work_description ?? '',
];

// セッションから作業報告情報を取得
$workReport = Session::pull('workReport') ?? [];
$workReport += $workReportDefault;

// セッションに年月情報を格納
Session::set('workReportYyyymm', $postData['yyyymm']);

$smarty->assign('PAGE_TITLE', "作業報告登録");
$smarty->assign('CSS_FILE_NAME', "workReport/regist");
$smarty->assign('JS_FILE_NAME', "workReport/regist");
$smarty->assign(compact('worker', 'workReport', 'workFlagSelectAllList', 'workFlagSelectList'));
$smarty->assign('MAIN_HTML', $smarty->fetch('workReport/regist.tpl'));

/**
 * 作業報告一覧のバリデーション
 *
 * @param $postData POSTデータ
 * @return 検証済みのPOSTデータ
 */
function validateWorkReport($postData) {
    // 年月
    if (
        !isset($postData['yyyymm'])
        || !preg_match('/^(200[5-9]{1}|20[1-9]{1}[0-9]{1})\/(0[1-9]{1}|1[0-2]{1})$/', $postData['yyyymm'])
    ) {
        $postData['yyyymm'] = date('Y/m');
    }

    // 日（年月との組み合わせチェック）
    list($year, $month) = explode('/', $postData['yyyymm']);
    if (
        !isset($postData['day'])
        || !checkdate($month, $postData['day'], $year)
    ) {
        $postData['day'] = date('d');
    }

    return $postData;
}