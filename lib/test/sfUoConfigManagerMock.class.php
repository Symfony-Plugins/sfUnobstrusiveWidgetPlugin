<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Config manager mock.
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage test
 * @author     François Béliveau
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