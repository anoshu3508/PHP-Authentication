<?php
// General filepath constants
require dirname(__DIR__) . '/config/paths.php';

// Include the composer autoload file
require VENDOR . 'autoload.php';

$smarty = new Smarty();
$smarty->setTemplateDir(TEMPLATES);
$smarty->setCompileDir(TEMPLATES_COMPILE);
$smarty->setConfigDir(CONFIG);
$smarty->setCacheDir(CACHE);
$smarty->addPluginsDir(PLUGINS);

// ----------------------------------------------
// Ajax通信
// ----------------------------------------------
$ajaxApi = filter_input(INPUT_GET, 'api');
if ($ajaxApi === 'holidayJson') {
    require_once MODEL . 'ajax/holidayJson.php';
    exit;
}

require MODEL . 'common.php';