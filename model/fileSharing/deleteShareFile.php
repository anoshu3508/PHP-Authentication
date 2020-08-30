<?php
use Josantonius\File\File;
use Josantonius\Session\Session;

$message = '';
$errorFlag = false;
try {
    // トランザクション開始
    ORM::get_db()->beginTransaction();

    // トークンが無効な場合はエラー
    // ※ 直前のアクションがダウンロード処理の場合はチェック無視
    if (Session::get('before_action') !== 'downloadShareFile' && !$IS_VALID_TOKEN) {
        throw new Exception("再読み込みによる操作は無効です。");
    }

    // ファイルIDを取得
    $fileName = filter_input(INPUT_POST, 'file_name');

    // ファイル情報を取得
    $deleteFileList = ORM::for_table('file_sharing')
        ->where([
            'file_name' => $fileName,
            'upload_user_id' => $USER_INFO->id
        ])
        ->find_many();

    // レコードが存在しない場合
    if (empty($deleteFileList)) {
        throw new Exception('レコードの削除に失敗しました。対象レコードが存在しません。');
    }

    // ファイルパスを取得
    $filePath = WORK . 'fileSharing' . DS . $deleteFileList[0]->upload_user_id . DS . $deleteFileList[0]->file_name;

    // レコードを削除
    foreach ($deleteFileList as $deleteFile) {
        $deleteFile->delete();
    }

    // ファイルが存在しない場合はエラー
    if (!File::delete($filePath)) {
        throw new Exception('ファイルの削除に失敗しました。対象ファイルが存在しません。');
    }

    // コミット
    ORM::get_db()->commit();

    $message = '削除が完了しました。';
} catch (Exception $e) {
    // ロールバック
    ORM::get_db()->rollBack();
    $message = $e->getMessage();
    $errorFlag = true;
}

$smarty->assign('message', $message);
$smarty->assign('errorFlag', $errorFlag);

// ファイル共有画面の表示処理
require_once 'fileSharing.php';