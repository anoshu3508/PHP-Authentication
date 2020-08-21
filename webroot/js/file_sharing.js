$(function() {
    $('.share_selectbox select').SumoSelect({
        csvDispCount: 1,
        selectAll: true,
        okCancelInMulti: true,
        captionFormatAllSelected: "※「全員に共有」を<br/>チェックしてください" 
    });

    $('.delete_button').click(function() {
        $('input[name="delete_id"]').val($(this).find('a').attr('data-delete-id'));
    });
    // $(document).on('click', '.delete_button', function() {
    //     $('input[name="delete_id"]').val($(this).attr('data-delete-id'));
    // });
    
    $(document).on('confirmation', '.remodal', function() {
        var deleteId = $('input[name="delete_id"]').val();
        if (deleteId == '') {
            alert('削除に失敗しました。\n再度やり直してください。')
            return false;
        }
        postToDeleteShareFile(deleteId);
    });
    
    /**
     * 「ファイルのアップロード」を実行
     * 
     * @return {undefined}
     */
    // function postToUploadShareFile() {
    //     postToAction('uploadShareFile');
    // }
    
    /**
     * 「ファイルのダウンロード」を実行
     * 
     * @param {string} id
     * @return {undefined}
     */
    function postToDownloadShareFile(id) {
        $('<form/>',{action:"/employee/", method:"post"})
        .append("<input type='hidden' name='action' value='downloadShareFile'>")
        .append("<input type='hidden' name='file_id' value='" + id + "'>")
        .appendTo($('body'))
        .submit();
    }
    
    /**
     * 「ファイルを削除」を実行
     * 
     * @param {string} id
     * @return {undefined}
     */
    function postToDeleteShareFile(id) {
        $('<form/>',{action:"/employee/", method:"post"})
        .append("<input type='hidden' name='action' value='deleteShareFile'>")
        .append("<input type='hidden' name='file_id' value='" + id + "'>")
        .appendTo($('body'))
        .submit();
    }
})