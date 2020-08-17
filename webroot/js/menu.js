/**
 * 「ファイル共有」に移動
 * 
 * @return {undefined}
 */
function postToFileSharing() {
    $('<form/>',{action:"/employee/", method:"post"})
    .append("<input type='hidden' name='action' value='fileSharing'>")
    .appendTo($('body'))
    .submit();
}