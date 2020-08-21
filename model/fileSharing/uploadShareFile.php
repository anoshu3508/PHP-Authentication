<?php
use Verot\Upload\Upload;

$message = '';
try {
    // ファイルサイズを取得
    $fileSize = $_FILES['share_file']['size'];

    // ファイルサイズが20MBを超過する場合はエラー
    if ($fileSize >= 20971520) {
        throw new Exception('20MB以上のファイルはアップロード出来ません。')
    }

    // ファイルアップロード
    $filePath = uploadShareFile($_FILES['share_file'], $USER_INFO->id);

    // 共有者ID、全共有フラグを取得
    $shareUserIds = filter_input(INPUT_POST, 'share_user_id', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $shareAllFlag = filter_input(INPUT_POST, 'share_all_flg');

    // 全共有フラグが 1 の場合
    if ($shareAllFlag === '1') {
        $fileSharing = ORM::for_table('file_sharing')->create();
        $fileSharing->file_name = basename($filePath);
        $fileSharing->file_size = $fileSize;
        $fileSharing->upload_user_id = $USER_INFO->id;
        $fileSharing->share_all_flg = $shareAllFlag;
        $fileSharing->save();

    // 全共有フラグが 0 の場合
    } else {
        // 共有者ID毎にDBに登録
        foreach ($shareUserIds as $suId) {
            $fileSharing = ORM::for_table('file_sharing')->create();
            $fileSharing->file_name = basename($filePath);
            $fileSharing->file_size = $fileSize;
            $fileSharing->upload_user_id = $USER_INFO->id;
            $fileSharing->share_user_id = $suId;
            $fileSharing->save();
        }
    }

    $message = 'アップロードが完了しました。';
} catch (Exception $e) {
    $message = $e->getMessage();
}

$smarty->assign('message', $message);

// ファイル共有画面の表示処理
require_once 'fileSharing.php';

/**
 * ファイルをアップロード
 * 
 * @param $file ファイル
 * @return アップロードファイルのフルパス
 */
function uploadShareFile($file, $userId) {
    // ファイルハンドラーを生成
    $handle = new Upload($file, 'ja_JP');

    // アップロードの設定
    $handle->dir_chmod = 0755;       // ディレクトリが書き込めない場合に変更する属性
    $handle->no_script = false;      // テキストファイルに変換するか

    // 格納先ディレクトリを取得
    $directory = WORK . 'fileSharing' . DS . $userId;

    // アップロードファイルのチェック
    if ($handle->uploaded) {
        // 格納先ディレクトリを指定して保存
        $handle->process($directory);
        if ($handle->processed) {
            // アップロード成功
        } else {
            // アップロード処理失敗
            throw new Exception($handle->error);
        }
    } else {
        // アップロード失敗
        throw new Exception($handle->error);
    }

    return $handle->file_dst_pathname;
}