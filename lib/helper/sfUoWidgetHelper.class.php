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
   * Return true if sfDynamics plugin enabled, false otherwise
   *
   * @return boolean
   */
  public static function isDynamicsEnable()
  {
    //return false;
    return is_dir(sfConfig::get('sf_plugins_dir').'/sfDynamicsPlugin') && class_exists('sfDynamics');
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
  
  /**
   * Load all JS and CSS
   */
  static public function loadAll($adapter = 'jquery', $skin = 'default', $context=null)
  {
    try
    {
      self::addJavascript(self::getConfigManager($context)->getAllJavascripts($adapter));
      $stylesheets = self::getConfigManager($context)->getAllStylesheets($adapter, $skin);
      foreach ($stylesheets as $css)
      {
        self::addStylesheet($css, 'all');
      }
    }
    catch (Exception $e)
    {
      throw $e;
    }
  }
  
  /**
   * Load all JS and CSS for a specific widget
   */
  static public function load($adapter, $jsSelector, $jsTransformer, $skin)
  {
    try
    {
      self::addJavascript(sfUoWidgetHelper::getConfigManager()->getJavascripts($adapter, $jsSelector, $jsTransformer));
      $stylesheets = sfUoWidgetHelper::getConfigManager()->getStylesheets($adapter, $jsSelector, $jsTransformer, $skin);
      foreach ($stylesheets as $css)
      {
        $results[$css] = 'all';
      }
      self::addStylesheet($stylesheets);
    }
    catch (Excepion $e)
    {
      throw $e;
    }
  }
}