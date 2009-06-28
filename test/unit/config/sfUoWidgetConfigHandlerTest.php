<?php
$pluginPath = realpath(dirname(__FILE__).'/../../..');
include($pluginPath.'/test/bootstrap.php');
require_once($pluginPath.'/lib/config/sfUoWidgetConfigHandler.class.php');


$test = new lime_test(1, new lime_output_color());

$test->diag('sfUnobstrusiveWidgetPlugin : test sfUoWidgetConfigHandler class');

$uoWidgetConfigHandler = new sfUoWidgetConfigHandler();
$uoWidgetConfigHandler->initialize();

$test->diag('sfUoWidgetConfigHandler->parseYaml()');

$yamlConfig = $uoWidgetConfigHandler->execute(array($pluginPath.'/test/data/sfUoWidget.yml'));

$test->ok(is_string($yamlConfig), true, "->parseYaml() return string");

