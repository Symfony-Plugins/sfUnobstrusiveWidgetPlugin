<?php

/**
 * Default assets loader for sfUnobstrusiveWidgetPlugin.
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage lib.loader
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetDefaultLoader extends sfUoWidgetBaseLoader
{
  CONST TEMPLATE_JAVASCRIPT = '<script type="text/javascript" src="%s"></script>';
 	CONST TEMPLATE_STYLESHEET = '<link rel="stylesheet" type="text/css" media="%s" href="%s" />';

  protected
    $stylesheets = array(),
    $javascripts = array(),
    $request     = null;

  /**
   * Constructor
   */
  public function __construct(sfUoWidgetConfigManager $configManager)
  {
    parent::__construct($configManager);

    $context       = $this->configManager->getContext();
    $this->request = $context->getRequest();

    $context->getEventDispatcher()->connect('response.filter_content', array($this, 'filterResponseContent'));
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
      $html = $this->getAssetsAsHtml($content);

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

  protected function getAssetsAsHtml($content)
  {
    $assets = array();

    foreach ($this->javascripts as $javascript)
    {
      $js = $this->request->getRelativeUrlRoot().$javascript;

      if (false === strpos($js, $content))
      {
        $assets[] = sprintf(self::TEMPLATE_JAVASCRIPT, $js);
      }
    }

    foreach ($this->stylesheets as $media => $stylesheets)
    {
      foreach ($stylesheets as $stylesheet)
      {
        $css = $this->request->getRelativeUrlRoot().$stylesheet;

        if (false === strpos($css, $content))
        {
          $assets[] = sprintf(self::TEMPLATE_STYLESHEET, $media, $css);
        }
      }
    }

    return implode("\n", $assets);
  }
}