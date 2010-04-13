<?php

/**
 * sfUoWidgetFormInputTextMany
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage lib.widget.form
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetFormInputTextMany extends sfUoWidgetFormInputText
{
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * separator:              Separator to use
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfUoWidget
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
  
    $this->addOption('separator', ',');
  }

  /**
   * Gets the JavaScript selector.
   *
   * @return string A JS selector
   */
  protected function getJsSelector()
  {
    return 'uo_widget_form_input_text_many';
  }
  
  /**
    * @param  mixed $value      The value
    *
    * @return void
    */
  protected function setRenderValue($value)
  {
    if (is_array($value) || $value instanceof Iterator)
    {
      $values = $value;
      $value  = '';
      foreach ($values as $v)
      {
        if (!empty($value))
        {
          $value .= $this->getOption('separator');
        }
      
        $value .= $this->getItemValue($v);
      }
    }
    $this->renderValue = $value;
  }
  
  /**
    * @param  mixed $value      The value
    *
    * @return void
    */
  protected function getItemValue($value)
  {
    return $value;
  }
}