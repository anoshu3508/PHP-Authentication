$(function() {
    /**
     * ファイルアップロード
     */
    $('.file_upload form').submit(function() {
        var shareFile = $(this).find('#share_file').prop('files')[0];
        var shareUserIdSelected = $(this).find('#share_user_id option:selected');
        var shareAllFlagChecked = $(this).find('#share_all_flag').prop('checked');
        var privateFlagChecked = $(this).find('#private_flag').prop('checked');
        if (shareFile == undefined) {
            alert('ファイルを選択してください。');
            return false;
        }
        if (shareFile.size >= 20971520) {
            alert('20MB以上のファイルはアップロード出来ません。');
            return false;
        }
        if (
            shareUserIdSelected.length == 0
            && !shareAllFlagChecked
            && !privateFlagChecked
        ) {
            alert('共有する人を選択してください。');
            return false;
        }
    });

    /**
     * 「全員に共有」チェックボックス
     */
    $('.file_upload #share_all_flag').change(function() {
        if ($('.file_upload #private_flag').prop('checked')) {
            $('.file_upload #private_flag').prop('checked', false);
        }
    });
    /**
     * 「非公開」チェックボックス
     */
    $('.file_upload #private_flag').change(function() {
        if ($('.file_upload #share_all_flag').prop('checked')) {
            $('.file_upload #share_all_flag').prop('checked', false);
        }
    });

    /**
     * 「共有する人」セレクトボックス
     */
    $('.file_upload #share_user_id').SumoSelect({
        csvDispCount: 2,
        selectAll: true,
        okCancelInMulti: true,
        captionFormatAllSelected: "全員に共有"
    });
    $('.file_upload #share_user_id').change(function() {
        // チェックボックスをオフ
        if ($('.file_upload #share_all_flag').prop('checked')) {
            $('.file_upload #share_all_flag').prop('checked', false);
        }
        if ($('.file_upload #private_flag').prop('checked')) {
            $('.file_upload #private_flag').prop('checked', false);
        }
        // 全選択の場合「全員に共有」チェックボックスをオン
        var optLength = $(this).children('option').length;
        var selectedLength = $(this).children('option:selected').length;
        if (optLength == selectedLength) {
            $('.file_upload #share_all_flag').prop('checked', true);
        }

    });

    /**
     * 削除ボタン
     */
    $('.upload_file_list .delete_button').click(function() {
        $('input[name="delete_file_name"]').val($(this).find('a').attr('data-delete-file-name'));
    });

    /**
     * 削除モーダルウィンドウ
     */
    $(document).on('confirmation', '.remodal', function() {
        var deleteFileName = $('input[name="delete_file_name"]').val();
        if (deleteFileName == '') {
            alert('削除に失敗しました。\n再度やり直してください。')
            return false;
        }
        postToDeleteShareFile(deleteFileName);
    });
})

/**
 * 「ファイルのダウンロード」を実行
 *
 * @param {string} id
 * @return {undefined}
 */
function postToDownloadShareFile(id) {
    $('<form/>',{action:"/employee/", method:"post"})
    .append('<input type="hidden" name="action" value="downloadShareFile">')
    .append('<input type="hidden" name="csrf_token" value="' + CSRF_TOKEN + '">')
    .append('<input type="hidden" name="file_id" value="' + id + '">')
    .appendTo($('body'))
    .submit();
}

/**
 * 「ファイルを削除」を実行
 *
 * @param {string} fileName
 * @return {undefined}
 */
function postToDeleteShareFile(fileName) {
    $('<form/>',{action:"/employee/", method:"post"})
    .append('<input type="hidden" name="action" value="deleteShareFile">')
    .append('<input type="hidden" name="csrf_token" value="' + CSRF_TOKEN + '">')
    .append('<input type="hidden" name="file_name" value="' + fileName + '">')
    .appendTo($('body'))
    .submit();
}