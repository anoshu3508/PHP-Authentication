/**
 * 「ログアウト」を実行
 * 
 * @return {undefined}
 */
function postToLogout() {
    $('<form/>',{action:"/employee/", method:"post"})
    .append("<input type='hidden' name='action' value='logout'>")
    .appendTo($('body'))
    .submit();
}