<?php
// データベース接続に必要な設定値を取得
if (strpos($_SERVER['SERVER_NAME'], 'activezero.co.jp') !== false) {
    $dbconf = parse_ini_file(CONFIG . 'database.ini', true)['product'];
} else {
    $dbconf = parse_ini_file(CONFIG . 'database.ini', true)['develop'];
}

// 通常のデータベース接続
$connString  = $dbconf['driver'] . ':';
$connString .= 'host=' . $dbconf['host'] . ';';
$connString .= 'dbname=' . $dbconf['database'] . ';';
$connString .= 'charset=' . $dbconf['charset'];
ORM::configure([
    'connection_string' => $connString,
    'username'          => $dbconf['username'],
    'password'          => $dbconf['password'],
]);

// 年を取得
$year = filter_input(INPUT_POST, 'year');
if (!isset($year) || !preg_match('/^(200[5-9]{1}|20[1-9]{1}[0-9]{1})$/', $year)) {
    $year = date('Y');
}

// 祝日マスタから祝日情報を取得
$holidayMstMonth = ORM::for_table('holiday_mst')
    ->select_many_expr([
        'date' => 'DATE_FORMAT(date, "%Y/%m/%d")',
    ])
    ->where_raw('date BETWEEN ? AND ?', [$year . '/01/01', $year . '/12/31'])
    ->order_by_asc('date')
    ->find_many();

// 今月分の祝日を配列に格納
$holidayList = [];
if (!empty($holidayMstMonth)) {
    foreach ($holidayMstMonth as $holiday) {
        $holidayList[] = $holiday->date;
    }
}

header("Content-Type: application/json; charset=utf-8");
echo json_encode($holidayList);