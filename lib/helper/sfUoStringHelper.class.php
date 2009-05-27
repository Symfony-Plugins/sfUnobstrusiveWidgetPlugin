<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoStringHelper
 * String helper for sfUnobstrusiveWidgetPlugin.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoStringHelper
{
  /**
   * Return a camelized string with first letter in lowercase
   *
   * @param string $value
   *
   * @return string
   */
  public static function camelizeLcFirst($value)
  {
    $result = sfInflector::camelize($value);
    return strtolower(substr($result, 0, 1)).substr($result, 1);
  }
}