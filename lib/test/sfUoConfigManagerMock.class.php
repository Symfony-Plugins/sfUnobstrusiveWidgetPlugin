<?php

/**
 * Config manager mock.
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage lib.test
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoConfigManagerMock
{
  protected
    $lazyMode = true,
    $adapter  = 'jquery',
    $transformerTemplate = 'jQuery("#%1$s").%2$s({ %3$s });';
    
  public function setLazyMode($value)
  {
    $this->lazyMode = $value;
  }
  
  public function setAdapter($value)
  {
    $this->adapter = $value;
  }

  public function isInLazyModeByDefault()
  {
    return $this->lazyMode;
  }
  
  public function getDefaultAdapter()
  {
    return $this->adapter;
  }
  
  public function getTransformerTemplate($jsAdapter, $jsSelector, $transformer)
  {
    return $this->transformerTemplate;
  }
}