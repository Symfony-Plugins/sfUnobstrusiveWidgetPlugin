<?php

/**
 * I18n mock.
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage lib.test
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoI18nMock
{
  public function __($message, $options = array(), $catalogue = 'message')
  {
    return strtr($message, $options);
  }
}