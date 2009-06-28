<?php
$pluginPath = realpath(dirname(__FILE__).'/../../..');
include($pluginPath.'/test/bootstrap.php');
require_once($pluginPath.'/lib/config/sfUoAdminMenuConfigHandler.class.php');


$test = new lime_test(1, new lime_output_color());

$test->diag('sfUnobstrusiveWidgetPlugin : test sfUoAdminMenuConfigHandler class');

$uoWidgetConfigHandler = new sfUoAdminMenuConfigHandler();
$uoWidgetConfigHandler->initialize();

$test->diag('sfUoAdminMenuConfigHandler->parseYaml()');

$yamlConfig = $uoWidgetConfigHandler->execute(array($pluginPath.'/test/data/sfUoAdminMenu.yml'));

$test->ok(is_string($yamlConfig), true, "->parseYaml() return string");