// $(document).on('click', 'a', function() {
//     $(this).click(function () {
//         alert('只今処理中です。\nそのままお待ちください。');
//         return false;
//     });
// });
new ScrollHint('.table-responsive', {
    scrollHintIconAppendClass: 'scroll-hint-icon-white', // white-icon will appear
    applyToParents: true,
    i18n: {
      scrollable: 'スクロールできます'
    }
});

/**
 * アクションを実行
 * 
 * @param {string} action
 * @return {undefined}
 */
function postToAction(action) {
    $('<form/>',{action:"/employee/", method:"post"})
    .append('<input type="hidden" name="action" value="' + action + '">')
    .append('<input type="hidden" name="csrf_token" value="' + CSRF_TOKEN + '">')
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