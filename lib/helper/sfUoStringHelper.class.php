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

  /**
   * Returns a JavaScript configuration.
   *
   * @param  array $data
   *
   * @return string A JS configuration
   */
  public static function getJavascriptConfiguration(array $data)
  {
    return implode(',', array_map(array('sfUoStringHelper', 'getJavascriptConfigurationCallback'), array_keys($data), array_values($data)));
  }

  /**
   * Prepares a JavaScript configuration key and value for HTML representation.
   *
   * It removes empty attributes, except for the value one.
   *
   * @param  string $k  The config key
   * @param  string $v  The config value
   *
   * @return string The HTML representation of the JS config key attribute pair.
   */
  public static function getJavascriptConfigurationCallback($k, $v)
  {
    if (empty($k) || (empty($v) && !is_bool($v) && !is_numeric($v)))
    {
      return null;
    }

    switch (true)
    {
      case is_array($v):
        $v = implode(',', array_map(array('sfUoStringHelper', 'getJavascriptConfigurationCallback'), array_keys($v), array_values($v)));
        if (is_integer($k))
        {
          return empty($v) ? '' : sprintf('{ %s }', $v);
        }
        else
        {
          $template = substr($v, 0, 1) == '{' ? '%s: [%s]' : '%s: {%s}';
          return empty($v) ? '' : sprintf($template, $k, $v);
        }
        break;

      case is_bool($v):
        $v = $v ? 'true' : 'false';
        break;

      case is_int($v):
      case is_float($v):
      case is_numeric($v):
        break;

      case is_string($v):
        if (false !== strpos($k, '()'))
        {
          //function
          $k = str_replace('()', '', $k);
          $v = $v;
        }
        else
        {
          $v = '"'.$v.'"';
        }
        break;

      default:
        throw new Exception('Invalid value');
    }

    return sprintf('%s: %s', $k, $v);
  }
}