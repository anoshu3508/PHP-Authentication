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

// 作業報告情報を取得
$workReport = [
    'customer' => filter_input(INPUT_POST, 'customer') ?? '',
    'work_flag' => filter_input(INPUT_POST, 'work_flag') ?? '0',
    'date' => filter_input(INPUT_POST, 'date') ?? date('Y/m/d'),
    'start_time' => filter_input(INPUT_POST, 'start_time') ?? '09:00',
    'end_time' => filter_input(INPUT_POST, 'end_time') ?? '18:00',
    'break_hours' => filter_input(INPUT_POST, 'break_hours') ?? '1.00',
    'operation_hours' => '', // 稼働時間はバリデーション後に計算
    'overtime_hours' => filter_input(INPUT_POST, 'overtime_hours') ?? '0.00',
    'holiday_hours' => filter_input(INPUT_POST, 'holiday_hours') ?? '0.00',
    'midnight_hours' => filter_input(INPUT_POST, 'midnight_hours') ?? '0.00',
    'work_description' => filter_input(INPUT_POST, 'work_description') ?? '',
];

$message = '';
$errorFlag = false;
try {
    // トークンが無効な場合はエラー
    if (!$IS_VALID_TOKEN) {
        throw new Exception("再読み込みによる操作は無効です。");
    }

    // バリデーション実行（エラーの場合は例外にスロー）
    validateRegistWorkReport($workReport, $workFlagSelectList);

    // 稼働時間を計算
    $workReport['operation_hours'] = calcOperationHours(
        $workReport['start_time'],
        $workReport['end_time'],
        $workReport['break_hours']
    );

    // 勤務フラグの表示用文字列を取得
    if (array_key_exists($workReport['work_flag'], $workFlagSelectList)) {
        $workReport['work_flag_name'] = $workFlagSelectList[$workReport['work_flag']];
    }

    // セッションに作業報告情報を登録
    Session::set('workReport', $workReport);

} catch (Exception $e) {
    $message = $e->getMessage();
    $errorFlag = true;
}

// 作業報告登録画面でエラー表示
if ($errorFlag) {
    $smarty->assign('PAGE_TITLE', "作業報告登録");
    $smarty->assign('CSS_FILE_NAME', "workReport/regist");
    $smarty->assign('JS_FILE_NAME', "workReport/regist");
    $smarty->assign(compact('worker', 'workReport', 'workFlagSelectList', 'message', 'errorFlag'));
    $smarty->assign('MAIN_HTML', $smarty->fetch('workReport/regist.tpl'));
// 作業報告確認画面へ遷移
} else {
    $smarty->assign('PAGE_TITLE', "作業報告確認");
    $smarty->assign('CSS_FILE_NAME', "workReport/confirm");
    $smarty->assign('JS_FILE_NAME', "workReport/confirm");
    $smarty->assign(compact('worker', 'workReport'));
    $smarty->assign('MAIN_HTML', $smarty->fetch('workReport/confirm.tpl'));
}

/**
 * 作業報告登録のバリデーション
 *
 * @param $workReport 作業報告情報
 * @param $workFlagSelectList 勤務フラグ一覧
 */
function validateRegistWorkReport($workReport, $workFlagSelectList) {
    $validFlag = true;
    $message = '';

    // 客先
    if ($workReport['customer'] === '') {
        $validFlag = false;
        $message .= '客先は必須です。<br/>';
    } elseif (mb_strlen($workReport['customer']) > 255) {
        $validFlag = false;
        $message .= '客先は255文字以下で入力してください。<br/>';
    }

    // 勤務フラグ
    if ($workReport['work_flag'] === '') {
        $validFlag = false;
        $message .= '勤務フラグは必須です。<br/>';
    } elseif (!isset($workFlagSelectList[$workReport['work_flag']])) {
        $validFlag = false;
        $message .= '勤務フラグの値が不正です。<br/>';
    }

    // 日付
    if ($workReport['date'] === '') {
        $validFlag = false;
        $message .= '日付は必須です。<br/>';
    } else {
        list($year, $month, $day) = explode('/', $workReport['date']);
        if (!checkdate($month, $day, $year)) {
            $validFlag = false;
            $message .= '日付は「YYYY/MM/DD」の形式で入力してください。<br/>';
        } elseif (
            !preg_match(
                '/^(200[5-9]{1}|20[1-9]{1}[0-9]{1})$/',
                $year
            )
        ) {
            $validFlag = false;
            $message .= '日付は2005～2099年の間を指定してください。<br/>';
        }
    }

    // 開始時刻
    if ($workReport['start_time'] === '') {
        $validFlag = false;
        $message .= '開始時刻は必須です。<br/>';
    } elseif (
        !preg_match(
            '/^(0[0-9]{1}|1{1}[0-9]{1}|2{1}[0-3]{1}):(0[0-9]{1}|[1-5]{1}[0-9]{1})$/',
            $workReport['start_time']
        )
    ) {
        $validFlag = false;
        $message .= '開始時刻は「hh/mm」の形式で入力してください。<br/>';
    }

    // 終了時刻
    if ($workReport['end_time'] === '') {
        $validFlag = false;
        $message .= '終了時刻は必須です。<br/>';
    } elseif (
        !preg_match(
            '/^(0[0-9]{1}|1{1}[0-9]{1}|2{1}[0-3]{1}):(0[0-9]{1}|[1-5]{1}[0-9]{1})$/',
            $workReport['end_time']
        )
    ) {
        $validFlag = false;
        $message .= '終了時刻は「hh/mm」の形式で入力してください。<br/>';
    }

    // 休憩時間
    if ($workReport['break_hours'] === '') {
        $validFlag = false;
        $message .= '休憩時間は必須です。<br/>';
    } elseif (
        !is_numeric($workReport['break_hours'])
        || !preg_match(
            '/^([0-9]{1,2})\.([0-9]{2})$/',
            $workReport['break_hours']
        )
    ) {
        $validFlag = false;
        $message .= '休憩時間は半角で入力してください。<br/>';
    }

    // 残業時間
    if (
        $workReport['overtime_hours'] !== ''
        && (
            !is_numeric($workReport['overtime_hours'])
            || !preg_match(
                '/^([0-9]{1,2})\.([0-9]{2})$/',
                $workReport['overtime_hours']
            )
        )
    ) {
        $validFlag = false;
        $message .= '残業時間は半角で入力してください。<br/>';
    }

    // 休出時間
    if (
        $workReport['holiday_hours'] !== ''
        && (
            !is_numeric($workReport['holiday_hours'])
            || !preg_match(
                '/^([0-9]{1,2})\.([0-9]{2})$/',
                $workReport['holiday_hours']
            )
        )
    ) {
        $validFlag = false;
        $message .= '休出時間は半角で入力してください。<br/>';
    }

    // 深夜時数
    if (
        $workReport['midnight_hours'] !== ''
        && (
            !is_numeric($workReport['midnight_hours'])
            || !preg_match(
                '/^([0-9]{1,2})\.([0-9]{2})$/',
                $workReport['midnight_hours']
            )
        )
    ) {
        $validFlag = false;
        $message .= '深夜時数は半角で入力してください。<br/>';
    }

    // 作業内容
    if (
        $workReport['work_description'] !== ''
        && mb_strlen($workReport['work_description']) > 255
    ) {
        $validFlag = false;
        $message .= '作業内容は255文字以下で入力してください。<br/>';
    }

    // バリデーションエラーの場合
    if (!$validFlag) {
        throw new Exception($message);
    }
}

/**
 * 稼働時間を計算
 *
 * @param $startTime 開始時刻
 * @param $endTime 終了時刻
 * @param $breakHours 休憩時間
 * @return 稼働時間
 */
function calcOperationHours($startTime, $endTime, $breakHours) {
    if ($startTime === '' || $endTime === '' || $breakHours === '') {
        return '0.00';
    }

    $startTimeStamp = strtotime($startTime);
    $endTimeStamp = strtotime($endTime);
    $interval = $endTimeStamp - $startTimeStamp;
    if ($interval < 0) {
        $endTimeStamp = $endTimeStamp + (3600 * 24);
        $interval = $endTimeStamp - $startTimeStamp;
    }
    $operationTimeStamp = $interval - ($breakHours * 3600);

    return number_format($operationTimeStamp / 3600, 2);
}