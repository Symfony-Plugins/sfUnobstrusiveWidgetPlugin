<?php

/**
 * sfUoWidgetFormPropelSelectMany
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage lib.widget.orm.propel.form
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetFormPropelSelectMany extends sfUoWidgetFormPropelSelect
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