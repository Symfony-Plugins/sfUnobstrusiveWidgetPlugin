<?php

/**
 * sfDynamicPlugin assets loader for sfUnobstrusiveWidgetPlugin.
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage lib.loader
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