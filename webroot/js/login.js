function createAccountLink() {
    $('<form/>',{action:"/admin/", method:"post"})
    .append("<input type='hidden' name='action' value='userRegistInput'>")
    .appendTo($('body'))
    .submit();
}