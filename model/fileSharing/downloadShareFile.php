<?php
use Apfelbox\FileDownload\FileDownload;

// ファイルIDを取得
$fileId = filter_input(INPUT_POST, 'file_id');

// ファイル名を取得
$downloadFileInfo = ORM::for_table('file_sharing')
    ->select_many(
        'id',
        'file_name',
        'upload_user_id'
    )
    ->where_raw(
        '(`id` = ? AND (`upload_user_id` = ? OR `share_user_id` = ?))',
        [$fileId, $USER_INFO->id, $USER_INFO->id]
    )
    ->find_one();

$fileDownload = FileDownload::createFromFilePath(WORK . 'fileSharing' . DS . $downloadFileInfo->upload_user_id . DS . $downloadFileInfo->file_name);
$fileDownload->sendDownload("download.pdf");

// ファイル共有画面の表示処理
require_once 'fileSharing.php';