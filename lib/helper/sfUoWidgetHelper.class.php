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