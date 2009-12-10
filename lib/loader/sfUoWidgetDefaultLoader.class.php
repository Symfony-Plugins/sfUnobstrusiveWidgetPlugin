<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetDefaultLoader
 * Default assets loader for sfUnobstrusiveWidgetPlugin.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetDefaultLoader extends sfUoWidgetBaseLoader
{
  CONST TEMPLATE_JAVASCRIPT = '<script type="text/javascript" src="%1$s"></script>';
  CONST TEMPLATE_STYLESHEET = '<link rel="stylesheet" type="text/css" media="%1$s" href="%2$s" />';
  
  protected
    $stylesheets   = array(),
    $javascripts   = array();
  
  /**
   * Constructor
   */
  public function __construct(sfUoWidgetConfigManager $configManager)
  {
    parent::__construct($configManager);
    $this->configManager->getContext()->getEventDispatcher()->connect('response.filter_content', array($this, 'filterResponseContent'));
  }
  
  public function loadTheme($theme)
  {
    try
    {
      $this->addStylesheets($this->configManager->getThemeStylesheets($theme));
    }
    catch (Excepion $e)
    {
      throw $e;
    }
  }

  public function loadTransformers($jsAdapter, $jsSelector, array $jsTransformers)
  {
    try
    {
      $this->configManager->checkTransformersCompatibilities($jsAdapter, $jsSelector, $jsTransformers);
    }
    catch (Excepion $e)
    {
      throw $e;
    }
    
    parent::loadTransformers($jsAdapter, $jsSelector, $jsTransformers);
  }

  public function loadTransformer($jsAdapter, $jsSelector, $jsTransformer)
  {
    try
    {
      parent::loadTransformer($jsAdapter, $jsSelector, $jsTransformer);
      $this->addStylesheets($this->configManager->getTransformerStylesheets($jsAdapter, $jsSelector, $jsTransformer));
      $this->addJavascripts($this->configManager->getTransformerJavascripts($jsAdapter, $jsSelector, $jsTransformer));
    }
    catch (Excepion $e)
    {
      throw $e;
    }
  }
  
  public function filterResponseContent(sfEvent $event, $content)
  {
    if (false !== ($pos = strpos($content, '</head>')))
    {
      $html = $this->getAssetsAsHtml();

      if ($html)
      {
        $content = substr($content, 0, $pos)."\n".$html.substr($content, $pos);
      }
    }
    return $content;
  }
  
  public function initJavascripts()
  {
    $this->javascripts = array();
  }
  
  public function initStylesheets()
  {
    $this->stylesheets = array();
  }
  
  public function getJavascripts()
  {
    return $this->javascripts;
  }
  
  public function getStylesheets()
  {
    return $this->stylesheets;
  }
  
  protected function addStylesheets(array $stylesheets)
  {
    foreach($stylesheets as $stylesheet)
    {
      if (is_array($stylesheet))
      {
        $this->addStylesheets($stylesheet);
      }
      else
      {
        $this->addStylesheet($stylesheet);
      }
    }
  }
  
  protected function addStylesheet($stylesheet, $media = 'all')
  {
    if (!array_key_exists($media, $this->stylesheets))
    {
      $this->stylesheets[$media] = array();
    }
    
    if (!in_array($stylesheet, $this->stylesheets[$media]))
    {
      $this->stylesheets[$media][] = $stylesheet;
    }
  }
  
  protected function addJavascripts(array $javascripts)
  {
    foreach($javascripts as $javascript)
    {
      if (is_array($javascript))
      {
        $this->addJavascripts($javascript);
      }
      else
      {
        $this->addJavascript($javascript);
      }
    }
  }
  
  protected function addJavascript($javascript)
  {
    if (!in_array($javascript, $this->javascripts))
    {
      $this->javascripts[] = $javascript;
    }
  }
  
  protected function getAssetsAsHtml()
  {
    $assets = array();

    foreach ($this->javascripts as $javascript)
    {
      $assets[] = sprintf(self::TEMPLATE_JAVASCRIPT, $javascript);
    }
    
    foreach ($this->stylesheets as $media=>$stylesheets)
    {
      foreach ($stylesheets as $stylesheet)
      {
        $assets[] = sprintf(self::TEMPLATE_STYLESHEET, $media, $stylesheet);
      }
    }
    
    return implode("\n", $assets);
  }
}