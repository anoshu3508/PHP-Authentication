<?php
// ユーザ一覧を取得
$userList = ORM::for_table('users')
    ->where_not_equal('id', $USER_INFO->id)
    ->find_many();

// アップロードファイル一覧を取得
$uploadFileList = ORM::for_table('file_sharing')
    ->select_many(
        'file_sharing.id',
        'file_sharing.file_name',
        'file_sharing.file_size',
        'file_sharing.updated_at',
        'users.last_name'
    )
    ->select_expr(
        'DATE_FORMAT(file_sharing.updated_at, "%Y/%m/%d %H:%i")', 'uploaded_at'
    )
    ->inner_join(
        'users', ['file_sharing.share_user_id', '=', 'users.id']
    )
    ->where('upload_user_id', $USER_INFO->id)
    ->find_many();

// 共有ファイル一覧を取得
$shareFileList = ORM::for_table('file_sharing')
    ->select_many(
        'file_sharing.id',
        'file_sharing.file_name',
        'file_sharing.file_size',
        'file_sharing.updated_at',
        'users.last_name'
    )
    ->select_expr(
        'DATE_FORMAT(file_sharing.updated_at, "%Y/%m/%d %H:%i")', 'uploaded_at'
    )
    ->inner_join(
        'users', ['file_sharing.upload_user_id', '=', 'users.id']
    )
    ->where('share_user_id', $USER_INFO->id)
    ->find_many();

$smarty->assign('PAGE_TITLE', "ファイル共有");
$smarty->assign('CSS_FILE_NAME', "file_sharing");
$smarty->assign('JS_FILE_NAME', "file_sharing");
$smarty->assign('userList', $userList);
$smarty->assign('uploadFileList', $uploadFileList);
$smarty->assign('shareFileList', $shareFileList);
$smarty->assign('MAIN_HTML', $smarty->fetch('fileSharing/file_sharing.tpl'));