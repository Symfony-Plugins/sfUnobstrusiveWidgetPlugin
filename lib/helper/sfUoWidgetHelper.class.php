<?php

/**
 * Main helper for sfUnobstrusiveWidgetPlugin.
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage lib.helper
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetHelper
{
  protected static
    $configManager          = null,
    $adminMenuConfigManager = null,
    $loader;

  /**
   * Return loader
   *
   * @param  mixte
   *
   * @return object extends sfUoWidgetBaseLoader
   */
  public static function getLoader($context=null)
  {
    if (is_null(self::$loader))
    {
      $loaderClass  = self::isDynamicsEnable() ? 'sfUoWidgetDynamicsLoader' : 'sfUoWidgetDefaultLoader';
      self::$loader = new $loaderClass(self::getConfigManager($context));
    }

    return self::$loader;
  }

  /**
   * Return config manager
   *
   * @param  mixte
   *
   * @return sfUoWidgetConfigManager
   */
  public static function getConfigManager($context=null)
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
  public static function getAdminMenuConfigManager($context=null)
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
    return is_dir(sfConfig::get('sf_plugins_dir').'/sfDynamicsPlugin') && class_exists('sfDynamics');
  }
}