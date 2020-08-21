// $(document).on('click', 'a', function() {
//     $(this).click(function () {
//         alert('只今処理中です。\nそのままお待ちください。');
//         return false;
//     });
// });

/**
 * アクションを実行
 * 
 * @param {string} action
 * @return {undefined}
 */
function postToAction(action) {
    $('<form/>',{action:"/employee/", method:"post"})
    .append("<input type='hidden' name='action' value='" + action + "'>")
    .appendTo($('body'))
    .submit();
}

/**
 * 「メニュー」に移動
 * 
 * @return {undefined}
 */
function postToMenu() {
    postToAction('menu');
}

/**
 * 「ログアウト」を実行
 * 
 * @return {undefined}
 */
function postToLogout() {
    postToAction('logout');
}