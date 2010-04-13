<?php

/**
 * sfUoWidgetFormSelectMany
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage lib.widget.form
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetFormSelectMany extends sfUoWidgetFormSelect
{
  /**
   * Configures the current widget.
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormDate
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->setOption('multiple', true);
  }
}