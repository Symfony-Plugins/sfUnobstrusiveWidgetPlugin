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
    $data = array_map(array('sfUoStringHelper', 'getJavascriptConfigurationCallback'), array_keys($data), array_values($data));
    foreach ($data as $k => $v)
    {
      if ('' === $v || is_null($v))
      {
        unset($data[$k]);
      }
    }
    
    return implode(', ', $data);
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
    if (is_string($k) && false !== strpos($k, '()'))
    {
      //function
      $k = str_replace('()', '', $k);
      $v = $v;
    }
    else
    {
      $v = self::getJavascriptConfigurationValue($v);
    }

    if (empty($k) || '' === $v || is_null($v))
    {
      return null;
    }

    return sprintf('%s: %s', $k, $v);
  }

  /**
   * Return a configuration value.
   *
   * @param  string $v  The config value
   *
   * @return mixed
   */
  protected static function getJavascriptConfigurationValue($v)
  {
    if (is_object($v))
    {
      // try to transform object to string
      $v = (string)$v;
    }
  
    switch (true)
    {
      case is_array($v):
        $result  = array();
        $isArray = true;
        foreach ($v as $key => $value)
        {
          if (false === strpos($key, '()'))
          {
            $value = self::getJavascriptConfigurationValue($value);
          }
          else
          {
            $key = str_replace('()', '', $key);
          }
          
          if ('' !== $value && !is_null($value))
          {
            if (!is_int($key))
            {
              $isArray = false;
              $value   = $key.': '.$value;
            }

            $result[] = $value;
          }
        }

        if (empty($result))
        {
          return null;
        }
        else
        {
          return $isArray ? sprintf('[%s]', implode(', ', $result)) : sprintf('{%s}', implode(', ', $result));
        }

      case is_bool($v):
        $v = $v ? 'true' : 'false';
        break;

      case is_int($v):
      case is_float($v):
      case is_numeric($v):
        break;

      case is_string($v):
        $v = empty($v) ? null : json_encode($v);
        break;
      
      case is_null($v):
        break;

      default:
        throw new Exception('Invalid value');
    }
    
    if ('' !== $v && !is_null($v))
    {
      return $v;
    }

    return null;
  }
}