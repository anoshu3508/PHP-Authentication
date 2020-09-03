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
        'file_sharing.upload_user_id',
        'file_sharing.updated_at'//,
        // 'users.last_name'
    )
    ->select_many_expr([
        'share_name' => '
            CASE
                WHEN file_sharing.share_all_flag = 1 THEN "全員"
                WHEN file_sharing.share_user_id IS NULL THEN "非公開"
                ELSE GROUP_CONCAT(users.last_name separator "<br/>")
            END
        ',
        // 'uploaded_at' => 'DATE_FORMAT(file_sharing.updated_at, "%Y/%m/%d %H:%i")'
        'uploaded_at' => 'DATE_FORMAT(file_sharing.updated_at, "%Y/%m/%d")'
    ])
    ->left_outer_join(
        'users', ['file_sharing.share_user_id', '=', 'users.id']
    )
    ->where('upload_user_id', $USER_INFO->id)
    ->group_by([
        'file_sharing.file_name',
        'file_sharing.upload_user_id'
    ])
    ->order_by_desc('file_sharing.id')
    ->find_many();

// 共有ファイル一覧を取得
$shareFileList = ORM::for_table('file_sharing')
    ->select_many(
        'file_sharing.id',
        'file_sharing.file_name',
        'file_sharing.file_size',
        'file_sharing.updated_at',
        ['owner_name' => 'users.last_name']
    )
    ->select_expr(
        // 'DATE_FORMAT(file_sharing.updated_at, "%Y/%m/%d %H:%i")', 'uploaded_at'
        'DATE_FORMAT(file_sharing.updated_at, "%Y/%m/%d")', 'uploaded_at'
    )
    ->left_outer_join(
        'users', ['file_sharing.upload_user_id', '=', 'users.id']
    )
    ->where_any_is([
        ['share_user_id'  => $USER_INFO->id],
        ['share_all_flag' => '1', 'upload_user_id' => $USER_INFO->id]
    ], [
        'upload_user_id' => '<>'
    ])
    ->order_by_desc('file_sharing.id')
    ->find_many();

$smarty->assign('PAGE_TITLE', "ファイル共有");
$smarty->assign('CSS_FILE_NAME', "file_sharing");
$smarty->assign('JS_FILE_NAME', "file_sharing");
$smarty->assign(compact('userList', 'uploadFileList', 'shareFileList'));
$smarty->assign('MAIN_HTML', $smarty->fetch('fileSharing/file_sharing.tpl'));