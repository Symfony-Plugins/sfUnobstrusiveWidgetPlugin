<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetLoader
 * Base assets loader class for sfUnobstrusiveWidgetPlugin.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
abstract class sfUoWidgetBaseLoader
{
  protected
    $configManager = null,
    $isFirstLoad   = true;
  
  /**
   * Constructor
   */
  public function __construct(sfUoWidgetConfigManager $configManager)
  {
    $this->configManager = $configManager;
  }

  public function loadTransformers($jsAdapter, $jsSelector, array $jsTransformers)
  {
    foreach ($jsTransformers as $jsTransformer)
    {
      $this->loadTransformer($jsAdapter, $jsSelector, $jsTransformer);
    }
  }
  
  public function loadTransformer($jsAdapter, $jsSelector, $jsTransformer)
  {
    if ($this->isFirstLoad)
    {
      $this->isFirstLoad = false;

      try
      {
        $this->loadTheme($this->configManager->getAdapterTheme($jsAdapter));
      }
      catch (Excepion $e)
      {
        throw $e;
      }
    }
  }
  
  abstract public function loadTheme($theme);
}