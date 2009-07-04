<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetFormDoctrineInputTextMany represents an input text HTML.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau
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