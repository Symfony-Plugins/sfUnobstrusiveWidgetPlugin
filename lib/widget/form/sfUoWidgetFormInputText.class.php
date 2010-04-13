<?php

/**
 * sfUoWidgetFormInputText
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage lib.widget.form
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetFormInputText extends sfUoWidgetFormInput
{
  /**
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormInput
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->setOption('type', 'text');
  }
}