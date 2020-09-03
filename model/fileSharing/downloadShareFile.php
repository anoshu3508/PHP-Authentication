<?php
use Apfelbox\FileDownload\FileDownload;
use Josantonius\File\File;

$message = '';
$errorFlag = false;
try {
    // ファイルIDを取得
    $fileId = filter_input(INPUT_POST, 'file_id');

    // ファイル情報を取得
    $downloadFileInfo = ORM::for_table('file_sharing')
        ->select_many(
            'id',
            'file_name',
            'upload_user_id'
        )
        ->where_any_is([
            ['upload_user_id' => $USER_INFO->id],
            ['share_user_id' => $USER_INFO->id],
            ['share_all_flag' => '1']
        ])
        ->find_one($fileId);
        // ->where_raw(
        //     '(`id` = ? AND (`upload_user_id` = ? OR `share_user_id` = ? OR `share_all_flag` = ?))',
        //     [$fileId, $USER_INFO->id, $USER_INFO->id, '1']
        // )
        // ->find_one();

    // レコードが存在しない場合
    if (!$downloadFileInfo) {
        throw new Exception('ダウンロードに失敗しました。対象レコードが見つかりません。');
    }

    // ファイルパスを取得
    $filePath = WORK . 'fileSharing' . DS . $downloadFileInfo->upload_user_id . DS . $downloadFileInfo->file_name;

    // ファイルが存在しない場合はエラー
    if (!File::exists($filePath)) {
        throw new Exception('ダウンロードに失敗しました。ファイルが存在しません。');
    }

    $fileDownload = FileDownload::createFromFilePath($filePath);
    $fileDownload->sendDownload($downloadFileInfo->file_name);
} catch (Exception $e) {
    $message = $e->getMessage();
    $errorFlag = true;
}

$smarty->assign(compact('message', 'errorFlag'));

// ファイル共有画面の表示処理
require_once 'fileSharing.php';