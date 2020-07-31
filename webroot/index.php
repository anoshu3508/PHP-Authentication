<?php
// Include the composer autoload file
require '../vendor/autoload.php';

$smarty = new Smarty();
$smarty->template_dir = '../templates';
$smarty->compile_dir = '../templates_c';
$smarty->config_dir = '../configs';
$smarty->cache_dir = '../cache';

require '../models/common.php';