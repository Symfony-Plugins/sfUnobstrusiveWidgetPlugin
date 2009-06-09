<?php
$pluginPath = dirname(__FILE__).'/../../../..';

include($pluginPath.'/test/bootstrap.php');

require_once($pluginPath.'/lib/widget/base/sfUoWidget.class.php');
require_once($pluginPath.'/lib/helper/sfUoWidgetHelper.class.php');
require_once($pluginPath.'/lib/test/sfUoConfigManagerMock.class.php');
require_once($pluginPath.'/lib/test/sfUoLoaderMock.class.php');
require_once($pluginPath.'/lib/test/sfUoControllerMock.class.php');
require_once($pluginPath.'/lib/test/sfUoI18nMock.class.php');
require_once($pluginPath.'/lib/test/sfUoWidgetMock.class.php');

$t = new lime_test(16, new lime_output_color());


$t->diag('default');
$object = new sfUoWidgetMock(array(
  'config_manager'  => new sfUoConfigManagerMock(),
  'loader'          => new sfUoLoaderMock(),
  'controller'      => new sfUoControllerMock(),
  'i18n'            => new sfUoI18nMock(),
));

$t->is($object->getJsTransformers(), array(), 'getJsTransformers() return empty array by default');
$t->is($object->hasJsTransformer(), false, 'hasJsTransformer() return false by default');
$t->is($object->getJsAdapter(), 'jquery', 'getJsAdapter() return "jquery" by default');
$t->is($object->getRenderName(), null, 'getRenderName() return null by default');
$t->is($object->getId(), null, 'getId() return null by default');
$t->is($object->getRenderAttributes(), array(), 'getRenderAttributes() return empty array by default');
$t->is($object->getRenderValue(), null, 'getRenderValue() return null by default');
$t->is($object->getJsSelector(), 'uo_widget', 'getJsSelector() return "uo_widget"');
$t->is($object->getJsClass(), $object->getJsSelector(), 'getRenderValue() return same value as getJsSelector() by default');
$t->is($object->getJsClasses(), array('uo_widget'), 'getJsClasses() return array("uo_widget") by default');
$t->is($object->getJsConfig($object->getId()), '', 'getJsConfig() return empty string by default');


$t->diag('utils');
$object1 = new sfUoWidgetMock(array(
  'config_manager'  => new sfUoConfigManagerMock(),
  'loader'          => new sfUoLoaderMock(),
  'controller'      => new sfUoControllerMock(),
  'i18n'            => new sfUoI18nMock(),
));
$object2 = new sfUoWidgetMock(
  array(
    'config_manager'  => new sfUoConfigManagerMock(),
    'loader'          => new sfUoLoaderMock(),
    'controller'      => new sfUoControllerMock(),
    'i18n'            => new sfUoI18nMock(),
  ),
  array('test'=>'test')
);
$attributes = array('foo'=>'bar');

$t->is($object1->getMergedAttributes($attributes), array('foo'=>'bar'), 'getMergedAttributes() return merged attributes');
$t->is($object1->getMergedAttributes($attributes, false), array('foo'=>'bar'), 'getMergedAttributes() return merged attributes');
$t->is($object1->getMergedAttributes($attributes, true), array('foo'=>'bar', 'class'=>'uo_widget',), 'getMergedAttributes() return merged attributes');
$t->is($object2->getMergedAttributes($attributes, false), array('test'=>'test', 'foo'=>'bar'), 'getMergedAttributes() return merged attributes');
$t->is($object2->getMergedAttributes($attributes, true), array('test'=>'test', 'foo'=>'bar', 'class'=>'uo_widget',), 'getMergedAttributes() return merged attributes');