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
$smarty->setPluginsDir(PLUGINS);

require MODEL . 'common.php';