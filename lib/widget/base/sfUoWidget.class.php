<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidget
 * Base class for all unobstrusive widget.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
abstract class sfUoWidget extends sfWidgetForm
{
  const INIT_TEMPLATE_JQUERY    = '$("#%1$s").%2$s({});';
  const ON_LOAD_TEMPLATE_JQUERY = 'jQuery(document).ready(function(){%1$s})';

  protected
    $renderAttributes = array(),
    $renderName       = '',
    $renderValue      = null;

  /**
   * Return the JS transformers
   *
   * @return array JS transformers
   */
  public function getJsTransformers()
  {
    $transformer = $this->getOption('js_transformer');
    if (empty($transformer))
    {
      $transformer = array();
    }

    return !is_array($transformer) ? array($transformer) : $transformer;
  }

  /**
   * Has JS transformer ?
   *
   * @return boolean
   */
  public function hasJsTransformer()
  {
    $transformer = $this->getOption('js_transformer');
    return !empty($transformer);
  }

  /**
   * Return init template
   *
   * @return string
   */
  public function getInitTemplate()
  {
    $result = $this->getOption('js_init_template');
    return is_null($result) ? sfConfig::get('app_sfUoWidgetPlugin_init_template', self::INIT_TEMPLATE_JQUERY) : $result;
  }

  /**
   * Return windowOnLoad template
   *
   * @return string
   */
  public function getOnLoadTemplate()
  {
    $result = $this->getOption('js_on_load_template');
    return is_null($result) ? sfConfig::get('app_sfUoWidgetPlugin_on_load_template', self::ON_LOAD_TEMPLATE_JQUERY) : $result;
  }

  /**
   * Is in "lazy" mode ?
   *
   * @return boolean
   */
  public function isLazy()
  {
    return $this->getOption('js_lazy');
  }

  /**
   * Return the JS skin
   *
   * @return string A JS skin
   */
  public function getJsSkin()
  {
    return $this->getOption('js_skin');
  }

  /**
   * Return the JS adapter
   *
   * @return string A JS adapter
   */
  public function getJsAdapter()
  {
    return $this->getOption('js_adapter');
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The value displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidget
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $attributes['id'] = $this->generateId($name);
    $attributes       = $this->getMergedAttributes($attributes, $this->hasJsTransformer());

    $this->setRenderName($name);
    $this->setRenderValue($value);
    $this->setRenderAttributes($attributes);

    $config = '';
    if ($this->hasJsTransformer())
    {
      $config = $this->getJsConfig($attributes['id']);
      $this->loadAssets();
    }

    return $this->doRender().$config;
  }

 /**
   * Gets the stylesheet paths associated with the widget.
   *
   * The array keys are files and values are the media names (separated by a ,):
   *
   *   array('/path/to/file.css' => 'all', '/another/file.css' => 'screen,print')
   *
   * @return array An array of stylesheet paths
   *
   * @see sfWidget
   */
  public function getStylesheets()
  {
    $manager      = sfUoWidgetHelper::getConfigManager();
    $transformers = $this->getJsTransformers();
    $results      = array();

    foreach ($transformers as $transformer)
    {
      $stylesheets = $manager->getStylesheets($this->getJsAdapter(), $this->getJsSelector(), $transformer, $this->getJsSkin());
      foreach ($stylesheets as $css)
      {
        $results[$css] = 'all';
      }
    }

    return $results;
  }

  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array An array of JavaScript paths
   *
   * @see sfWidget
   */
  public function getJavaScripts()
  {
    $manager      = sfUoWidgetHelper::getConfigManager();
    $transformers = $this->getJsTransformers();
    $results      = array();

    foreach ($transformers as $transformer)
    {
      $results = array_merge($results, $manager->getJavascripts($this->getJsAdapter(), $this->getJsSelector(), $transformer));
    }

    return $results;
  }

  /**
   * Gets the JavaScript configuration.
   *
   * @return string A JS configuration
   */
  public function getJsConfig($id)
  {
    $config = $this->getOption('js_config');
    if ((empty($config) && !$this->isLazy()) || empty($id))
    {
      return '';
    }

    $jsAdapter      = $this->getJsAdapter();
    $jsSelector     = $this->getJsSelector();
    $jsTransformers = $this->getJsTransformers();
    $result         = array();

    if (count($jsTransformers) == 1 && (!isset($config[$jsTransformers[0]]) || !is_array($config[$jsTransformers[0]])))
    {
      $config = array($jsTransformers[0] => $config);
    }

    $id = $this->getJsId($id);
    foreach ($this->getJsTransformers() as $transformer)
    {
      if (isset($config[$transformer]))
      {
        $result[] = $jsSelector.'_'.$transformer.'_config.'.$id.'={'.implode(',', array_map(array($this, 'getJsConfigCallback'), array_keys($config[$transformer]), array_values($config[$transformer]))).'};';
      }

      if ($this->isLazy())
      {
        $widgetInitialization = sprintf($this->getInitTemplate(), $id, sfUoWidgetHelper::camelizeLcFirst($jsSelector.'_'.$transformer));
        if (sfUoWidgetHelper::getConfigManager()->haveToSetsInWindowOnLoad($jsAdapter, $jsSelector, $transformer))
        {
          $result[] = sprintf($this->getOnLoadTemplate(), $widgetInitialization);
        }
        else
        {
          $result[] = $widgetInitialization;
        }
      }
    }

    return empty($result) ? '' : $this->renderContentTag('script', implode("\n", $result), array('type'=>'text/javascript'));
  }

  /**
   * Return JS config id.
   *
   * @return string The JS id
   */
  public function getJsId($id)
  {
    return $id;
  }

  /**
   * @return string An HTML tag string
   *
   * @see render()
   */
  abstract protected function doRender();

  /**
   * Load assets to response
   */
  protected function loadAssets()
  {
    try
    {
      sfUoWidgetHelper::getConfigManager()->checkTransformersCompatibilities($this->getJsAdapter(), $this->getJsSelector(), $this->getJsTransformers());
    }
    catch (Excepion $e)
    {
      throw $e;
    }

    if (sfUoWidgetHelper::isDynamicsEnable())
    {
      foreach ($this->getJsTransformers() as $transformer)
      {
        sfDynamics::load($this->getJsSelector().'.'.$transformer.'.'.$this->getJsAdapter());
      }
    }
    else
    {
      sfUoWidgetHelper::addJavascript($this->getJavaScripts());
      sfUoWidgetHelper::addStylesheet($this->getStylesheets());
    }
  }

  /**
    * @param  string $value        The name
    *
    * @return void
    */
  protected function setRenderName($value)
  {
    $this->renderName = $value;
  }

  /**
    * @return string The name
    */
  protected function getRenderName()
  {
    return $this->renderName;
  }

  /**
    * @return string The id
    */
  protected function getId()
  {
    $attributes = $this->getRenderAttributes();
    return array_key_exists('id', $attributes) ? $attributes['id'] : null;
  }

  /**
   * @param  mixed $values        The values
   *
   * @return void
   */
  protected function setRenderAttributes(Array $values)
  {
    $this->renderAttributes = $values;
  }

  /**
   * @param  string $name        The name
   *
   * @return array The render attributes
   */
  protected function getRenderAttributes()
  {
    return $this->renderAttributes;
  }

  /**
    * @param  mixed $values        The value
    *
    * @return void
    */
  protected function setRenderValue($value)
  {
    $this->renderValue = $value;
  }

  /**
    * @return mixed The value
    */
  protected function getRenderValue()
  {
    return $this->renderValue;
  }

  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * js_transformer:        JS transformer to apply (null by default)
   *  * js_config:             Associative array with JS options (empty array by default)
   *  * js_adapter:            The JS adapter ("jquery" by default)
   *  * js_skin:               The JS adapter ("default" by default)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidget
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('js_transformer', array());
    $this->addOption('js_config', array());
    $this->addOption('js_adapter', sfConfig::get('app_sfUoWidgetPlugin_adapter', 'jquery'));
    $this->addOption('js_skin', sfConfig::get('app_sfUoWidgetPlugin_skin', 'default'));
    $this->addOption('js_lazy', sfConfig::get('app_sfUoWidgetPlugin_lazy', true));
    $this->addOption('i18n_catalogue', 'messages');
  }

  /**
   * Gets the JavaScript selector.
   *
   * @return string A JS selector
   */
  protected function getJsSelector()
  {
    return 'uo_widget';
  }

  /**
   * Gets the JavaScript class.
   *
   * @return string A JS class
   */
  protected function getJsClass()
  {
    return implode(' ', $this->getJsClasses());
  }

  /**
   * Gets the JavaScript classes.
   *
   * @return Array JS classes
   */
  protected function getJsClasses()
  {
    $jsSelector = $this->getJsSelector();
    $result     = array();
    if ($this->hasJsTransformer())
    {
      foreach ($this->getJsTransformers() as $transformer)
      {
        $result[] = $jsSelector.'_'.$transformer;
      }
    }
    else
    {
      $result[] = $jsSelector;
    }

    return $result;
  }

  /**
   * Gets the merged attributes.
   *
   * @param array $attributes     An array of attributes
   *
   * @return array An array of merged attributes
   */
  protected function getMergedAttributes(Array $attributes, $mergeJsClass = false)
  {
    $attributes = array_merge($this->attributes, $attributes);
    if ($mergeJsClass)
    {
      $attributes = $this->addAttribute($attributes, 'class', $this->getJsClass());
    }

    return $attributes;
  }

  /**
   * Add an attribute.
   *
   * @param array $attributes     An array of attributes
   * @param string $key           An attribute name
   * @param mixed $value          An array or a string defined the attribute
   *
   * @return array An array of merged attributes
   */
  protected function addAttribute(Array $attributes, $key, $value)
  {
    if (empty($value))
    {
      return $attributes;
    }

    if (is_array($value))
    {
      $value = implode(' ', $value);
    }

    if (array_key_exists($key, $attributes))
    {
      $attributes[$key] .= ' '.$value;
    }
    else
    {
      $attributes[$key] = $value;
    }

    return $attributes;
  }

  /**
   * Prepares a JS config key and value for HTML representation.
   *
   * It removes empty attributes, except for the value one.
   *
   * @param  string $k  The config key
   * @param  string $v  The config value
   *
   * @return string The HTML representation of the JS config key attribute pair.
   */
  public function getJsConfigCallback($k, $v)
  {
    if (is_array($v))
    {
      $v = implode(',', array_map(array($this, 'getJsConfigCallback'), array_keys($v), array_values($v)));
      if (is_integer($k))
      {
        $result = empty($v) ? '' : sprintf('{%s}', $v);
      }
      else
      {
        $template = substr($v, 0, 1) == '{' ? '"%s":[%s]' : '"%s":{%s}';
        $result   = empty($v) ? '' : sprintf($template, $k, $v);
      }
    }
    else
    {
      if (is_bool($v))
      {
        $v = $v ? 'true' : 'false';
      }
      else if (false !== strpos($k, '()'))
      {
        //function
        $k = str_replace('()', '', $k);
        $v = $v;
      }
      else if (is_numeric($v))
      {
        $v = $v;
      }
      else
      {
        $v = '"'.$v.'"';
      }

      $result = (is_null($v) || '' === $v) ? '' : sprintf('"%s":%s', $k, $v);
    }

    return $result;
  }

  /** 
   * Returns the i18n version of the string 
   * if i18n is activated, or the string itself otherwise 
   * 
   * @todo handle placeholders 
   * @param string $string 
   * @return string 
   */ 

  protected function getI18n($string) 
  { 
    if (sfConfig::get('sf_i18n')) 
    { 
      return sfContext::getInstance()->getI18n()->__($string, array(), $this->getOption('i18n_catalogue')); 
    } 
    return $string; 
  }
}
