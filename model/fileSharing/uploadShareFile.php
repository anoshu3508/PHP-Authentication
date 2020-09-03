<?php
use Verot\Upload\Upload;
use Josantonius\Session\Session;

$message = '';
$errorFlag = false;
try {
    // トークンが無効な場合はエラー
    // ※ 直前のアクションがダウンロード処理の場合はチェック無視
    if (Session::get('before_action') !== 'downloadShareFile' && !$IS_VALID_TOKEN) {
        throw new Exception("再読み込みによる操作は無効です。");
    }

    // 共有者ID、全共有フラグを取得
    $shareUserIds = filter_input(INPUT_POST, 'share_user_id', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $shareAllFlag = filter_input(INPUT_POST, 'share_all_flag');
    $privateFlag = filter_input(INPUT_POST, 'private_flag');

    // 共有者が選択されていない場合はエラー
    if (!isset($shareUserIds) && !isset($shareAllFlag) && !isset($privateFlag)) {
        throw new Exception("共有する人を選択してください。");
    }

    // 共有者が自分の場合
    if (isset($shareUserIds) && in_array((int)$USER_INFO->id, $shareUserIds)) {
        throw new Exception("共有する人に自分は選択できません。");
    }

    // ファイルアップロード
    $filePath = uploadShareFile($_FILES['share_file'], $USER_INFO->id);

    // ファイル名、ファイルサイズを取得
    // ※ basename() と pathinfo() で不具合有。そのため explode() でファイル名を取得
    $filePathArr = explode(DS, $filePath);
    $fileName = array_pop($filePathArr);
    $fileSize = $_FILES['share_file']['size'];

    // 全員共有の場合
    if ($shareAllFlag === '1') {
        $fileSharing = ORM::for_table('file_sharing')->create();
        $fileSharing->file_name = $fileName;
        $fileSharing->file_size = $fileSize;
        $fileSharing->upload_user_id = $USER_INFO->id;
        $fileSharing->share_all_flag = $shareAllFlag;
        $fileSharing->save();

    // 非公開の場合
    } elseif ($privateFlag) {
        $fileSharing = ORM::for_table('file_sharing')->create();
        $fileSharing->file_name = $fileName;
        $fileSharing->file_size = $fileSize;
        $fileSharing->upload_user_id = $USER_INFO->id;
        $fileSharing->share_user_id = null;
        $fileSharing->save();

    // 個別共有の場合
    } else {
        // 共有者ID毎にDBに登録
        foreach ($shareUserIds as $suId) {
            $fileSharing = ORM::for_table('file_sharing')->create();
            $fileSharing->file_name = $fileName;
            $fileSharing->file_size = $fileSize;
            $fileSharing->upload_user_id = $USER_INFO->id;
            $fileSharing->share_user_id = $suId;
            $fileSharing->save();
        }
    }

    $message = 'アップロードが完了しました。';
} catch (Exception $e) {
    $message = $e->getMessage();
    $errorFlag = true;
}

$smarty->assign(compact('message', 'errorFlag'));

// ファイル共有画面の表示処理
require_once 'fileSharing.php';

/**
 * ファイルをアップロード
 * 
 * @param $file ファイル
 * @return アップロードファイルのフルパス
 */
function uploadShareFile($file, $userId) {
    // ファイルが正常にアップロードされていない場合
    if (
        !file_exists($file['tmp_name'])
        || !is_uploaded_file($file['tmp_name'])
    ) {
        throw new Exception('ファイルを選択してください。');
    }

    // ファイルサイズが20MBを超過する場合
    if ($file['size'] >= 20971520) {
        throw new Exception('20MB以上のファイルはアップロード出来ません。');
    }

    // ファイルハンドラーを生成
    $handle = new Upload($file, 'ja_JP');

    // アップロードの設定
    $handle->dir_chmod = 0755;       // ディレクトリが書き込めない場合に変更する属性
    $handle->mime_check = false;     // MIMEをチェックするか
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