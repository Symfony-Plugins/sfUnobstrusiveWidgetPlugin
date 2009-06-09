<?php
$pluginPath = dirname(__FILE__).'/../../..';

include($pluginPath.'/test/bootstrap.php');

require_once($pluginPath.'/lib/task/sfUoWidgetPluginUnpublishTask.class.php');


$t = new lime_test(0, new lime_output_color());


//todo