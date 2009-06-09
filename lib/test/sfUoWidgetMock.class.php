<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Widget mock.
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage test
 * @author     François Béliveau
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