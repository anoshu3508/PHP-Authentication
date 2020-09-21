<?php
use Illuminate\Support\Collection;
use League\Csv\Writer;
use League\Csv\CharsetConverter;
use Apfelbox\FileDownload\FileDownload;
use Josantonius\File\File;

// 年月を取得
$yyyymm = filter_input(INPUT_POST, 'yyyymm') ?? date('Y/m');
if (!preg_match('/(200[5-9]{1}|20[1-9]{1}[0-9]{1})\/([1-9]{1}|0[1-9]{1}|1[1-2]{1})/', $yyyymm)) {
    $yyyymm = date('Y/m');
}

// CSV用の作業報告情報を作成
$workReportCsvData = createWorkReportCsvData($USER_INFO, $yyyymm);

$message = '';
$errorFlag = false;
try {
    // 格納先ディレクトリ、ファイル名を取得
    $directory = WORK . 'workReport' . DS;
    $fileName = '作業報告書AZ-' . str_replace('/', '', $yyyymm) . '-' . $USER_INFO->last_name . '.csv';

    // CSVファイルを作成
    createWorkReportCsvFile($workReportCsvData, $directory, $fileName);

    // ファイルをダウンロード
    $fileDownload = FileDownload::createFromFilePath($directory . $fileName);
    $fileDownload->sendDownload($fileName);

    // ファイルを削除
    File::delete($directory . $fileName);

} catch (Exception $e) {
    $message = $e->getMessage();
    $errorFlag = true;
}

$smarty->assign(compact('message', 'errorFlag'));

// 作業報告一覧画面の表示処理
require_once 'workReport.php';

/**
 * CSV用の作業報告情報を作成
 *
 * @param $USER_INFO ユーザ情報
 * @param $yyyymm 年月
 * @return CSV用の作業報告情報
 */
function createWorkReportCsvData($USER_INFO, $yyyymm): array {
    // 年月を別々で取得
    list($year, $month) = explode('/', $yyyymm);

    // 月末日を取得
    $lastDay = date('t', strtotime($year . $month . '01'));
    // 1日の曜日を取得
    $firstWeek = date('w', strtotime($year . $month . '01'));

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
        'start_time' => 'TIME_FORMAT(start_time, "%k:%i")',
        'end_time' => 'TIME_FORMAT(end_time, "%k:%i")',
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

    $workReportCsvData = [];
    $dayOfWeek = $firstWeek;
    $worker = $USER_INFO->last_name . ' ' . $USER_INFO->first_name;
    $weekList = ['日', '月', '火', '水', '木', '金', '土'];
    $holidayCollection = new Collection($holidayMst);
    $wrdCollection = new Collection($workReportDaily);

    // ヘッダーを作成
    $workReportCsvData[0] = [
        '氏名',
        '客先',
        '日付',
        '曜日',
        '開始時刻',
        '終了時刻',
        '休憩時間',
        '稼働時間',
        '残業時間',
        '休出時間',
        '深夜時数',
        '作業内容',
        '勤務フラグ'
    ];

    // 1日から月末日までループ
    for ($day = 1; $day <= $lastDay; $day++) {
        // 対象日の作業報告情報と祝日マスタ情報を取得
        $date = date('Y/m/d', mktime(null, null, null, $month, $day, $year));
        $workReportFilter = $wrdCollection->first(function ($item) use($date) {
            return $item['date'] === $date;
        });
        $holidayFilter = $holidayCollection->first(function ($item) use($date) {
            return $item['date'] === $date;
        });

        // 休日フラグを取得
        $holidayFlag = false;
        if (isset($holidayFilter) || $dayOfWeek === 0 || $dayOfWeek === 6) {
            $holidayFlag = true;
        }

        // CSVデータを取得
        $workReportCsvData[$day][] = $worker;                                                // 氏名
        $workReportCsvData[$day][] = $workReportMonthly->customer ?? '';                     // 客先
        $workReportCsvData[$day][] = $date;                                                  // 日付
        $workReportCsvData[$day][] = $weekList[$dayOfWeek];                                  // 曜日
        $workReportCsvData[$day][] = $workReportFilter->start_time ?? '';                    // 開始時刻
        $workReportCsvData[$day][] = $workReportFilter->end_time ?? '';                      // 終了時刻
        $workReportCsvData[$day][] = $workReportFilter->break_hours ?? '';                   // 休憩時間
        $workReportCsvData[$day][] = $workReportFilter->operation_hours ?? '';               // 稼働時間
        $workReportCsvData[$day][] = $workReportFilter->overtime_hours ?? '';                // 残業時間
        $workReportCsvData[$day][] = $workReportFilter->holiday_hours ?? '';                 // 休出時間
        $workReportCsvData[$day][] = $workReportFilter->midnight_hours ?? '';                // 深夜時数
        $workReportCsvData[$day][] = $workReportFilter->work_description ?? '';              // 作業内容
        $workReportCsvData[$day][] = $workReportFilter->work_flag ?? ($holidayFlag ? 9 : 0); // 勤務フラグ

        $dayOfWeek++;
        // 7の場合は0（日曜）に変更
        if ($dayOfWeek === 7) {
            $dayOfWeek = 0;
        }
    }

    return $workReportCsvData;
}

/**
 * CSVファイルを作成
 * 
 * @param $workReportCsvData 作業報告情報のCSVデータ
 * @param $directory 格納先ディレクトリ
 * @param $fileName ファイル名
 */
function createWOrkReportCsvFile($workReportCsvData, $directory, $fileName) {
    // CSVのライターを作成(新規作成)
    $file = new SplTempFileObject();
    $writer = Writer::createFromFileObject($file);

    // 文字コードを設定
    $converter = (new CharsetConverter())
        ->inputEncoding('UTF-8')
        ->outputEncoding('SJIS-win');
    $writer->addFormatter($converter);
 
    // 書き込むデータを設定
 
    // 区切り文字を設定
    $writer->setDelimiter(",");
    // 囲い文字を設定
    $writer->setEnclosure('"');
    // エスケープを指定
    $writer->setEscape("\\");
    // 改行コードを設定
    $writer->setNewline("\r\n");
 
    // 複数のデータを一度に挿入
    $writer->insertAll($workReportCsvData);

    // ディレクトリがない場合は作成
    if (!File::exists($directory)) {
        File::createDir($directory);
    }

    // CSVファイルを書き出す
    $csvStr = $writer->getContent();
    file_put_contents($directory . $fileName, $csvStr);
}