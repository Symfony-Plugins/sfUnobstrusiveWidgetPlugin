<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetFormInputText represents an input text HTML.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau
 */
class sfUoWidgetFormInputText extends sfUoWidget
{
  /**
   * @return string An HTML tag string
   *
   * @see render()
   */
  protected function doRender()
  {
    return $this->renderTag('input', array_merge(array('type' => 'text', 'name' => $this->getRenderName(), 'value' => $this->getRenderValue()), $this->getRenderAttributes()));
  }
  
  /**
   * Gets the JavaScript selector.
   *
   * @return string A JS selector
   */
  protected function getJsSelector()
  {
    return 'uo_widget_form_input_text';
  }
}