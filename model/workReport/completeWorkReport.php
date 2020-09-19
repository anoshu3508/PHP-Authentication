<?php
use Josantonius\Session\Session;

$message = '';
$errorFlag = false;
try {
    // トランザクション開始
    ORM::get_db()->beginTransaction();

    // トークンが無効な場合はエラー
    if (!$IS_VALID_TOKEN) {
        throw new Exception("再読み込みによる操作は無効です。");
    }

    // セッションから作業報告情報を取得
    $workReport = Session::pull('workReport');

    // 作業報告情報を日別作業報告テーブルに登録
    registWorkReportDaily($workReport, $USER_INFO->id);
    // 作業報告情報を月別作業報告テーブルに登録
    registWorkReportMonthly($workReport, $USER_INFO->id);

    // コミット
    ORM::get_db()->commit();

    $message = '登録が完了しました。';
} catch (Exception $e) {
    // ロールバック
    ORM::get_db()->rollBack();
    $message = $e->getMessage();
    $errorFlag = true;
}

// セッションから作業報告情報を削除
Session::destroy('workReport');

$smarty->assign(compact('message', 'errorFlag'));

// 作業報告一覧画面の表示処理
require_once 'workReport.php';

/**
 * 作業報告情報を登録（日別作業報告テーブル）
 *
 * @param $workReport 作業報告情報
 */
function registWorkReportDaily($workReport, $user_id) {
    // 新規または更新かチェック
    $workReportDaily = ORM::for_table('work_report_daily')
        ->where([
            'date' => $workReport['date'],
            'user_id' => $user_id
        ])
        ->find_one();

    // 更新の場合
    if ($workReportDaily) {
        $workReportDaily->set([
            'work_flag' => $workReport['work_flag'],
            'start_time' => $workReport['start_time'],
            'end_time' => $workReport['end_time'],
            'break_hours' => $workReport['break_hours'],
            'operation_hours' => $workReport['operation_hours'],
            'overtime_hours' => $workReport['overtime_hours'],
            'holiday_hours' => $workReport['holiday_hours'],
            'midnight_hours' => $workReport['midnight_hours'],
            'work_description' => $workReport['work_description'],
        ]);
        $workReportDaily->set_expr('updated_at', 'NOW()');
        $workReportDaily->save();
    // 新規の場合
    } else {
        $workReportDailyORM = ORM::for_table('work_report_daily')->create();
        $workReportDailyORM->date = $workReport['date'];
        $workReportDailyORM->user_id = $user_id;
        $workReportDailyORM->work_flag = $workReport['work_flag'];
        $workReportDailyORM->start_time = $workReport['start_time'];
        $workReportDailyORM->end_time = $workReport['end_time'];
        $workReportDailyORM->break_hours = $workReport['break_hours'];
        $workReportDailyORM->operation_hours = $workReport['operation_hours'];
        $workReportDailyORM->overtime_hours = $workReport['overtime_hours'];
        $workReportDailyORM->holiday_hours = $workReport['holiday_hours'];
        $workReportDailyORM->midnight_hours = $workReport['midnight_hours'];
        $workReportDailyORM->work_description = $workReport['work_description'];
        $workReportDailyORM->save();
    }
}

/**
 * 作業報告情報を登録（月別作業報告テーブル）
 *
 * @param $workReport 作業報告情報
 */
function registWorkReportMonthly($workReport, $user_id) {
    // 年月を取得
    $dateArray = explode('/', $workReport['date']);
    $yyyymm = $dateArray[0] . $dateArray[1];

    // 新規または更新かチェック
    $workReportMonthly = ORM::for_table('work_report_monthly')
        ->where([
            'yyyymm' => $yyyymm,
            'user_id' => $user_id
        ])
        ->find_one();

    // 更新の場合
    if ($workReportMonthly) {
        // 客先が一致する場合は更新不要
        if ($workReportMonthly->customer !== $workReport['customer']) {
            $workReportMonthly->set([
                'customer' => $workReport['customer'],
            ]);
            $workReportMonthly->set_expr('updated_at', 'NOW()');
            $workReportMonthly->save();
        }
    // 新規の場合
    } else {
        $workReportMonthlyORM = ORM::for_table('work_report_monthly')->create();
        $workReportMonthlyORM->yyyymm = $yyyymm;
        $workReportMonthlyORM->user_id = $user_id;
        $workReportMonthlyORM->customer = $workReport['customer'];
        $workReportMonthlyORM->save();
    }
}