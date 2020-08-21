$(function() {
    $('.share_selectbox select').SumoSelect({
        csvDispCount: 2,
        selectAll: true,
        okCancelInMulti: true,
        captionFormatAllSelected: "※全員に共有する場合は<br/>「全員に共有」を<br/>チェックしてください" 
    });

    $('.delete_button').click(function() {
        $('input[name="delete_file_name"]').val($(this).find('a').attr('data-delete-file-name'));
    });
    // $(document).on('click', '.delete_button', function() {
    //     $('input[name="delete_file_name"]').val($(this).attr('data-delete-file-name'));
    // });
    
    $(document).on('confirmation', '.remodal', function() {
        var deleteFileName = $('input[name="delete_file_name"]').val();
        if (deleteFileName == '') {
            alert('削除に失敗しました。\n再度やり直してください。')
            return false;
        }
        postToDeleteShareFile(deleteFileName);
    });
    
    /**
     * 「ファイルのアップロード」を実行
     * 
     * @return {undefined}
     */
    // function postToUploadShareFile() {
    //     postToAction('uploadShareFile');
    // }
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