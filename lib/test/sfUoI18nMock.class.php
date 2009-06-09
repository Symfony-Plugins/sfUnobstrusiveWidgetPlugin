<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * I18n mock.
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage test
 * @author     François Béliveau
 */
class sfUoI18nMock
{
  public function __($message, $options = array(), $catalogue = 'message')
  {
    return strtr($message, $options);
  }
}