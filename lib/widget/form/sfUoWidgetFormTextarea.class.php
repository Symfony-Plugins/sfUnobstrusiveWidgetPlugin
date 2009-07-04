<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetFormTextarea represents a textarea HTML.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau
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