<?php

/**
 * Widget mock.
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage lib.test
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetMock extends sfUoWidget
{
  protected
    $render = '<p>foobar</p>';
  
  public function setRender($value)
  {
    $this->render = $value;
  }
  
  public function doRender()
  {
    return $this->$render;
  }
  
  public function getRenderName()
  {
    return parent::getRenderName();
  }
  
  public function getId()
  {
    return parent::getId();
  }
  
  public function getRenderAttributes()
  {
    return parent::getRenderAttributes();
  }
  
  public function getRenderValue()
  {
    return parent::getRenderValue();
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
  
  public function getMergedAttributes(array $attributes, $withClass = false)
  {
    return parent::getMergedAttributes($attributes, $withClass);
  }
}