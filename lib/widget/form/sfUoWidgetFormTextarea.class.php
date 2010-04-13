<?php

/**
 * sfUoWidgetFormTextarea
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage lib.widget.form
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetFormTextarea extends sfUoWidget
{
  /**
   * @return string An HTML tag string
   *
   * @see render()
   */
  protected function doRender()
  {
    $attributes = $this->getRenderAttributes();
    if (!isset($attributes['rows']))
    {
      $attributes['rows'] = 2;
    }
    if (!isset($attributes['cols']))
    {
      $attributes['cols'] = 2;
    }
  
    return $this->renderContentTag('textarea', $this->getRenderValue(), array_merge(array('name' => $this->getRenderName()), $attributes));
  }
  
  /**
   * Gets the JavaScript selector.
   *
   * @return string A JS selector
   */
  protected function getJsSelector()
  {
    return 'uo_widget_form_textarea';
  }
}