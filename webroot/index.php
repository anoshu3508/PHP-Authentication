<?php
// Include the composer autoload file
require '../vendor/autoload.php';

$smarty = new Smarty();
$smarty->template_dir = '../templates';
$smarty->compile_dir = '../templates_c';
$smarty->config_dir = '../configs';
$smarty->cache_dir = '../cache';

$action = filter_input(INPUT_POST, "action");

// GETの場合は、ログイン画面を表示
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    require_once '../models/login.php';

// POSTの場合は、指定された画面を表示
} elseif ($action !== null) {
    switch ($action) {
        case 'login':
            require_once '../models/login.php';
        default:
            require_once '../models/login.php';
    }
}