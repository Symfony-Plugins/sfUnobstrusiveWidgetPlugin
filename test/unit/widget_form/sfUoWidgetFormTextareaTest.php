<?php
$pluginPath = dirname(__FILE__).'/../../..';

include($pluginPath.'/test/bootstrap.php');

require_once($pluginPath.'/lib/widget/base/sfUoWidget.class.php');
require_once($pluginPath.'/lib/widget_form/sfUoWidgetFormTextArea.class.php');


$t = new lime_test(0, new lime_output_color());


//todo