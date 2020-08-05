function createAccountLink() {
    $('<form/>',{action:"/employee/", method:"post"})
    .append("<input type='hidden' name='action' value='userRegistInput'>")
    .appendTo($('body'))
    .submit();
}