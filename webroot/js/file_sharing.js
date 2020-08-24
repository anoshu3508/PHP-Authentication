$(function() {
    /**
     * 「全員に共有」チェックボックス
     */
    $('.share_checkbox #share_all_flag').change(function() {
        if ($('.share_checkbox #private_flag').prop('checked')) {
            $('.share_checkbox #private_flag').prop('checked', false);
        }
    });
    /**
     * 「非公開」チェックボックス
     */
    $('.share_checkbox #private_flag').change(function() {
        if ($('.share_checkbox #share_all_flag').prop('checked')) {
            $('.share_checkbox #share_all_flag').prop('checked', false);
        }
    });

    /**
     * 「共有する人」セレクトボックス
     */
    $('.share_selectbox select').SumoSelect({
        csvDispCount: 2,
        selectAll: true,
        okCancelInMulti: true,
        captionFormatAllSelected: "全員に共有" 
    });
    $('.share_selectbox select').change(function() {
        // チェックボックスをオフ
        if ($('.share_checkbox #share_all_flag').prop('checked')) {
            $('.share_checkbox #share_all_flag').prop('checked', false);
        }
        if ($('.share_checkbox #private_flag').prop('checked')) {
            $('.share_checkbox #private_flag').prop('checked', false);
        }
        // 全選択の場合「全員に共有」チェックボックスをオン
        var optLength = $(this).children('option').length;
        var selectedLength = $(this).children('option:selected').length;
        if (optLength == selectedLength) {
            $('.share_checkbox #share_all_flag').prop('checked', true);
        }

    });

    /**
     * 削除ボタン
     */
    $('.delete_button').click(function() {
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