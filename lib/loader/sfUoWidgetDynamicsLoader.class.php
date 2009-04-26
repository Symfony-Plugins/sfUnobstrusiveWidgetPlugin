<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetDefaultLoader
 * Default assets loader for sfUnobstrusiveWidgetPlugin.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetDynamicsLoader extends sfUoWidgetBaseLoader
{
  public function loadTheme($theme)
  {
    try
    {
      sfDynamics::load('uo_widget_theme.'.$theme);
    }
    catch (Excepion $e)
    {
      throw $e;
    }
  }

  public function loadTransformer($jsAdapter, $jsSelector, $jsTransformer)
  {
    try
    {
      parent::loadTransformer($jsAdapter, $jsSelector, $jsTransformer);
      sfDynamics::load($jsSelector.'.'.$jsTransformer.'.'.$jsAdapter);
    }
    catch (Excepion $e)
    {
      throw $e;
    }
  }
}