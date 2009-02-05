<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetHelper
 * General helper for sfUnobstrusiveWidgetPlugin.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetHelper
{
  static protected
    $configManager          = null,
    $adminMenuConfigManager = null;

  /**
   * Return config manager
   *
   * @param  mixte
   *
   * @return sfUoWidgetConfigManager
   */
  static public function getConfigManager($context=null)
  {
    if (is_null(self::$configManager))
    {
      self::$configManager = new sfUoWidgetConfigManager(is_null($context) ? sfContext::getInstance() : $context);
    }

    return self::$configManager;
  }

  /**
   * Return Admin menu config manager
   *
   * @param  mixte
   *
   * @return sfUoAdminMenuConfigManager
   */
  static public function getAdminMenuConfigManager($context=null)
  {
    if (is_null(self::$adminMenuConfigManager))
    {
      self::$adminMenuConfigManager = new sfUoAdminMenuConfigManager(is_null($context) ? sfContext::getInstance() : $context);
    }

    return self::$adminMenuConfigManager;
  }

  /**
   * Return plugin's web path
   *
   * @return string
   */
  static public function getWebPath()
  {
    return sfConfig::get('app_sfUoWidgetPlugin_js_path', '/sf_unobstrusive_widget');
  }
  
  /**
   * Return plugin's web js path
   *
   * @return string
   */
  static public function getWebJsPath()
  {
    return self::getWebPath().'/js';
  }
  
  /**
   * Return plugin's web css path
   *
   * @return string
   */
  static public function getWebCssPath()
  {
    return self::getWebPath().'/css';
  }

  /**
   * Add javascripts to response
   *
   * @param Array $values
   */
  static public function addJavascript(Array $values)
  {
    $response = sfContext::getInstance()->getResponse();
    foreach ($values as $js)
    {
      if (substr($js, 0, 1) != '/')
      {
        $js = self::getWebJsPath().'/'.$js;
      }
      $response->addJavascript($js, 'last');
    }
  }

  /**
   * Add stylesheets to response
   *
   * @param Array $values
   */
  static public function addStylesheet(Array $values)
  {
    $response = sfContext::getInstance()->getResponse();
    foreach ($values as $css => $media)
    {
      if (substr($css, 0, 1) != '/')
      {
        $css = self::getWebCssPath().'/'.$css;
      }
      $response->addStylesheet($css, 'last', array('media'=>$media));
    }
  }
  
  /**
   * Return a camelized string with first letter in lowercase
   *
   * @param string $value
   *
   * @return string
   */
  static public function camelizeLcFirst($value)
  {
    $result = sfInflector::camelize($value);
    return strtolower(substr($result, 0, 1)).substr($result, 1);
  }
}