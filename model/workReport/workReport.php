<?php
use Illuminate\Support\Collection;

// 作業者名を取得
$worker = $USER_INFO->last_name . ' ' . $USER_INFO->first_name;

// 年月を取得
$yyyymm = filter_input(INPUT_POST, 'yyyymm');
if (!preg_match('/(2[0-9]{3})\/([1-9]{1}|0[1-9]{1}|1[1-2]{1})/', $yyyymm)) {
    $yyyymm = date('Y/m');
}

// 月別の作業報告情報を取得
$workReportMonthly = ORM::for_table('work_report_monthly')
    ->where('user_id', $USER_INFO->id)
    ->where('yyyymm', str_replace('/', '', $yyyymm))
    ->find_one();
if (!$workReportMonthly) {
    $workReportMonthly = ORM::for_table('work_report_monthly')
        ->where('user_id', $USER_INFO->id)
        ->order_by_desc('yyyymm')
        ->find_one();
}

// 日別の作業報告一覧を取得
$workReportDaily = ORM::for_table('work_report_daily')
    ->select_many_expr([
        'date' => 'DATE_FORMAT(date, "%Y/%m/%d")',
    ])
    ->select_many([
        'work_flag',
        'start_time',
        'end_time',
        'break_hours',
        'operation_hours',
        'overtime_hours',
        'holiday_hours',
        'midnight_hours',
        'work_description'
    ])
    ->where('user_id', $USER_INFO->id)
    ->where_raw('date BETWEEN ? AND ?', [$yyyymm . '/01', $yyyymm . '/31'])
    ->order_by_asc('date')
    ->find_many();

// 祝日マスタから今月の祝日情報を取得
$holidayMst = ORM::for_table('holiday_mst')
    ->select_many_expr([
        'date' => 'DATE_FORMAT(date, "%Y/%m/%d")',
    ])
    ->select_many([
        'name',
        'company_flag'
    ])
    ->where_raw('date BETWEEN ? AND ?', [$yyyymm . '/01', $yyyymm . '/31'])
    ->order_by_asc('date')
    ->find_many();

// 月日別作業報告一覧を作成
list($year, $month) = explode('/', $yyyymm);
$workReportDateList = createWorkReportDateList($year, $month, $holidayMst, $workReportDaily);

$smarty->assign('PAGE_TITLE', "作業報告一覧");
$smarty->assign('CSS_FILE_NAME', "workReport/work_report");
$smarty->assign('JS_FILE_NAME', "workReport/work_report");
$smarty->assign(compact('worker', 'yyyymm', 'workReportMonthly', 'workReportDateList'));
$smarty->assign('MAIN_HTML', $smarty->fetch('workReport/work_report.tpl'));

/**
 * 月日別作業報告一覧を作成
 *
 * @param $year 年
 * @param $month 月
 * @param $holidayMst 祝日マスタ一覧
 * @param $workReportDaily 日別の作業報告一覧
 * @return 月日別作業報告一覧
 */
function createWorkReportDateList($year, $month, $holidayMst, $workReportDaily) {
    // 月末日を取得
    $lastDay = date('t', strtotime($year . $month . '01'));
    // 1日の曜日を取得
    $firstWeek = date('w', strtotime($year . $month . '01'));

    $workReportDateList = [];
    $dayOfWeek = $firstWeek;
    $weekList = ['日', '月', '火', '水', '木', '金', '土'];
    $wrdCollection = new Collection($workReportDaily);
    $holidayCollection = new Collection($holidayMst);

    // 1日から月末日までループ
    for ($day = 1; $day <= $lastDay; $day++) {
        // 曜日名を格納
        $workReportDateList[$day]['week_name'] = $weekList[$dayOfWeek];

        // 対象日の作業報告情報と祝日マスタ情報を取得
        $date = date('Y/m/d', mktime(null, null, null, $month, $day, $year));
        $workReportFilter = $wrdCollection->first(function ($item) use($date) {
            return $item['date'] === $date;
        });
        $holidayFilter = $holidayCollection->first(function ($item) use($date) {
            return $item['date'] === $date;
        });

        // 作業報告情報を格納
        if (isset($workReportFilter)) {
            $workReportDateList[$day] += json_decode(json_encode($workReportFilter), true);
        }

        // 祝日情報を格納
        $holidayInfo = [];
        if (isset($holidayFilter)) {
            $holidayInfo['holiday_flag'] = 1;
            $holidayInfo['holiday_name'] = $holidayFilter['name'];
            $holidayInfo['company_holiday_flag'] = $holidayFilter['company_flag'];
        } else {
            $holidayInfo['holiday_flag'] = ($dayOfWeek === 0 || $dayOfWeek === 6) ? 1 : 0;
            $holidayInfo['holiday_name'] = null;
            $holidayInfo['company_holiday_flag'] = 0;
        }
        $workReportDateList[$day] += json_decode(json_encode($holidayInfo), true);

        $dayOfWeek++;
        // 7の場合は0（日曜）に変更
        if ($dayOfWeek === 7) {
            $dayOfWeek = 0;
        }
    }

    return $workReportDateList;
}