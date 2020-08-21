<?php
// クッキー削除
setcookie('adminkey', '', time()-1);
// ページをリロード
header('Location: /employee/');
exit;