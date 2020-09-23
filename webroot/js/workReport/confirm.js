$(function() {
    /**
     * フォーム送信前処理
     */
    $(function() {
        var submitFlag = false;
        $('.confirm_work_report form').submit(function() {
            if (submitFlag) {
                return false;
            }
            submitFlag = true;
        });
    });
});

/**
 * 「作業報告確認」に移動
 *
 * @return {undefined}
 */
function postToRegistWorkReport() {
    postToAction('registWorkReport');
}