<?php
$pluginPath = dirname(__FILE__).'/../../../..';

include($pluginPath.'/test/bootstrap.php');

require_once($pluginPath.'/lib/widget/base/sfUoWidget.class.php');
require_once($pluginPath.'/lib/helper/sfUoWidgetHelper.class.php');


class sfUoWidgetTest extends sfUoWidget
{
  public function doRender()
  {
    return '<p>foo</p>';
  }
  
  public function insertAssets()
  {
    return parent::getName();
  }
  
  public function setRenderName($value)
  {
    return parent::setRenderName($value);
  }

  public function getRenderName()
  {
    return parent::getRenderName();
  }

  public function getId()
  {
    return parent::getId();
  }

  public function setRenderAttributes($values)
  {
    return parent::setRenderAttributes($values);
  }
  
  public function getRenderAttributes()
  {
    return parent::getRenderAttributes();
  }

  public function setRenderValue($value)
  {
    return parent::setRenderValue($value);
  }
  
  public function getRenderValue()
  {
    return parent::getRenderValue($value);
  }

  public function configure($options = array(), $attributes = array())
  {
    return parent::configure($options, $attributes);
  }

  public function getJsSelector()
  {
    return parent::getJsSelector();
  }
  
  public function getJsClass()
  {
    return parent::getJsClass();
  }
  
  public function getJsClasses()
  {
    return parent::getJsClasses();
  }

  public function getMergedAttributes($attributes, $mergeJsClass = false)
  {
    return parent::getMergedAttributes($attributes, $mergeJsClass);
  }

  public function addAttribute(Array $attributes, $key, $value)
  {
    return parent::addAttribute($attributes, $key, $value);
  }

  public function getJsConfig($id)
  {
    return parent::getJsConfig($id);
  }
}


$t = new lime_test(20, new lime_output_color());


$t->diag('default');
$object = new sfUoWidgetTest();

$t->is($object->getJsTransformers(), array(), 'getJsTransformers() return empty array by default');
$t->is($object->hasJsTransformer(), false, 'hasJsTransformer() return false by default');
$t->is($object->getJsSkin(), 'default', 'getJsSkin() return "default" by default');
$t->is($object->getJsAdapter(), 'jquery', 'getJsAdapter() return "jquery" by default');
$t->is($object->getRenderName(), null, 'getRenderName() return null by default');
$t->is($object->getId(), null, 'getId() return null by default');
$t->is($object->getRenderAttributes(), array(), 'getRenderAttributes() return empty array by default');
$t->is($object->getRenderValue(), null, 'getRenderValue() return null by default');
$t->is($object->getJsSelector(), 'uo_widget', 'getJsSelector() return "uo_widget"');
$t->is($object->getJsClass(), $object->getJsSelector(), 'getRenderValue() return same value as getJsSelector() by default');
$t->is($object->getJsClasses(), array('uo_widget'), 'getJsClasses() return array("uo_widget") by default');
$t->is($object->getJsConfig($object->getId()), '', 'getJsConfig() return empty string by default');


$t->diag('setter / getter');
$object = new sfUoWidgetTest();
$object->setRenderName('render_name');
$object->setRenderAttributes(array('foo'=>'bar'));
$object->setRenderValue('render_value');

$t->is($object->getRenderName(), 'render_name', 'setRenderName() sets renderName');
$t->is($object->getRenderAttributes(), array('foo'=>'bar'), 'setRenderAttributes() sets renderAttributes');
$t->is($object->getRenderValue(), 'render_value', 'setRenderValue() sets renderValue');


$t->diag('utils');
$object1    = new sfUoWidgetTest();
$object2    = new sfUoWidgetTest(array(), array('test'=>'test'));
$attributes = array('foo'=>'bar');

$t->is($object1->getMergedAttributes($attributes), array('foo'=>'bar'), 'getMergedAttributes() return merged attributes');
$t->is($object1->getMergedAttributes($attributes, false), array('foo'=>'bar'), 'getMergedAttributes() return merged attributes');
$t->is($object1->getMergedAttributes($attributes, true), array('foo'=>'bar', 'class'=>'uo_widget',), 'getMergedAttributes() return merged attributes');
$t->is($object2->getMergedAttributes($attributes, false), array('test'=>'test', 'foo'=>'bar'), 'getMergedAttributes() return merged attributes');
$t->is($object2->getMergedAttributes($attributes, true), array('test'=>'test', 'foo'=>'bar', 'class'=>'uo_widget',), 'getMergedAttributes() return merged attributes');