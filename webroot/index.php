<?php
// General filepath constants
require dirname(__DIR__) . '/config/paths.php';

// Include the composer autoload file
require VENDOR . 'autoload.php';

$smarty = new Smarty();
$smarty->template_dir = TEMPLATES;
$smarty->compile_dir = TEMPLATES_COMPILE;
$smarty->config_dir = CONFIG;
$smarty->cache_dir = CACHE;

require MODEL . 'common.php';