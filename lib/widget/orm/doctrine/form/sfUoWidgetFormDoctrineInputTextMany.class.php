<?php

/**
 * sfUoWidgetFormDoctrineInputTextMany
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage lib.widget.orm.doctrine.form
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetFormDoctrineInputTextMany extends sfUoWidgetFormInputTextMany
{
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * method:                 Method to get value
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfUoWidget
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    
    $this->addOption('method', 'getId');
  }

  /**
    * @param  mixed $value      The value
    *
    * @return void
    */
  protected function getItemValue($value)
  {
    if (is_object($value))
    {
      return call_user_func($value, $this->getOption('method'));
    }
    
    return parent::getItemValue($value);
  }
}