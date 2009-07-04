<?php
$pluginPath = dirname(__FILE__).'/../../../..';

include($pluginPath.'/test/bootstrap.php');

require_once($pluginPath.'/lib/widget/base/sfUoWidget.class.php');
require_once($pluginPath.'/lib/widget/form/sfUoWidgetFormSelect.class.php');
require_once($pluginPath.'/lib/widget/form/sfUoWidgetFormSelectMany.class.php');

$t = new lime_test(1, new lime_output_color());

$dom = new DomDocument('1.0', 'utf-8');
$dom->validateOnParse = true;

$t->diag('->render()');
$w = new sfUoWidgetFormSelectMany(array('choices' => array('foo' => 'bar', 'foobar' => 'foo')));
$t->is($w->getOption('multiple'), true, '__construct() creates a multiple select tag');
