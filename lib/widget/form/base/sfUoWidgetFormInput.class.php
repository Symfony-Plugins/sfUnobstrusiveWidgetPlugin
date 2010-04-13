<?php

/**
 * Base class for all unobstrusive widget form.
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage lib.widget.form.base
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetFormInput extends sfUoWidget
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * type: The widget type (text by default)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    $this->addOption('type', 'text');
  }

  /**
   * @return string An HTML tag string
   *
   * @see render()
   */
  protected function doRender()
  {
    return $this->renderTag('input', array_merge(array('type' => $this->getOption('type'), 'name' => $this->getRenderName(), 'value' => $this->getRenderValue()), $this->getRenderAttributes()));
  }
  
  /**
   * Gets the JavaScript selector.
   *
   * @return string A JS selector
   */
  protected function getJsSelector()
  {
    return 'uo_widget_form_input_'.$this->getOption('type');
  }
}
