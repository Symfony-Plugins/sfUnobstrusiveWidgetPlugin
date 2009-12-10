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
   * Is in "lazy" mode ?
   *
   * @return boolean
   */
  public function isLazy()
  {
    return !is_null($this->getOption('js_lazy')) ? $this->getOption('js_lazy') : $this->getConfigManager()->isInLazyModeByDefault();
  }

  /**
   * Return the JS adapter
   *
   * @return string A JS adapter
   */
  public function getJsAdapter()
  {
    return !is_null($this->getOption('js_adapter')) ? $this->getOption('js_adapter') : $this->getConfigManager()->getDefaultAdapter();
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
    //include_stylesheets_for_form include assets twice, so return an empty array
    return array();
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
    //include_stylesheets_for_form include assets twice, so return an empty array
    return array();
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
      if ($this->isLazy())
      {
        $template = $this->getConfigManager()->getTransformerTemplate($jsAdapter, $jsSelector, $transformer);
        $result[] = sprintf(
          $template,
          $id,
          sfUoStringHelper::camelizeLcFirst($jsSelector.'_'.$transformer),
          isset($config[$transformer]) ? sfUoStringHelper::getJavascriptConfiguration($config[$transformer]) : ''
        );
      }
    }

    return empty($result) ? '' : $this->renderContentTag('script', implode("\n", $result), array('type'=>'text/javascript'));
  }

  /**
   * Adds a value in an array defined in an option.
   *
   * @param string $option The option name
   * @param mixed  $value  The value to store in the option array
   * @param string $key    The key at where the value has to be located
   */
  public function optionConcatValue($option, $value, $key = null)
  {
    $array = $this->getOption($option);

    if (!is_array($array))
    {
      throw new InvalidArgumentException(sprintf('Option "%s" must be an array', $option));
    }

    if ($key && array_key_exists($key, $array))
    {
      throw new InvalidArgumentException(sprintf('Key "%s" already exists in "%s" option array', $key, $option));
    }

    if ($key)
    {
      $array[$key] = $value;
    }
    else
    {
      $array[] = $value;
    }

    $this->setOption($option, $array);
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
   * Returns a loader object
   *
   * @return sfUoWidgetBaseLoader or equivalent
   */
  public function getLoader()
  {
    return $this->getOption('loader') ? $this->getOption('loader') : sfUoWidgetHelper::getLoader();
  }
  
  /**
   * Load assets to response
   */
  public function loadAssets()
  {
    $this->getLoader()->loadTransformers($this->getJsAdapter(), $this->getJsSelector(), $this->getJsTransformers());
  }

  /**
   * @return string An HTML tag string
   *
   * @see render()
   */
  abstract protected function doRender();

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
  protected function setRenderAttributes(array $values)
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
   *  * js_adapter:            The JS adapter (see sfUoWidget.yml to defined the default value)
   *  * js_lazy:               Lazy mode enabled or not (see sfUoWidget.yml to defined the default value)
   *  * lazy_i18n:        Translate by default all widget labels or not
   *  * i18n_catalogue:        The i18n catalogue to use ("messages" by default)
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
    $this->addOption('js_adapter', null);
    $this->addOption('js_lazy', null);

    $this->addOption('lazy_i18n', true);
    $this->addOption('i18n_catalogue', 'messages');

    $this->addOption('config_manager', false);
    $this->addOption('loader', false);
    $this->addOption('controller', false);
    $this->addOption('i18n', false);
    $this->addOption('user', false);
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
   * @return array JS classes
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
  protected function getMergedAttributes(array $attributes, $mergeJsClass = false)
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
  protected function addAttribute(array $attributes, $key, $value)
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
   * Returns an i18n object
   *
   * @return sfI18n or equivalent
   */
  protected function getI18n()
  {
    return $this->getOption('i18n') ? $this->getOption('i18n') : $this->getContext()->getI18n();
  }

  /**
   * Returns a controller object
   *
   * @return sfWebController or equivalent
   */
  protected function getController()
  {
    return $this->getOption('controller') ? $this->getOption('controller') : $this->getContext()->getController();
  }

  /**
   * Returns a loader object
   *
   * @return sfUoWidgetBaseLoader or equivalent
   */
  protected function getConfigManager()
  {
    return $this->getOption('config_manager') ? $this->getOption('config_manager') : sfUoWidgetHelper::getConfigManager();
  }

  /**
   * Returns a user object
   *
   * @return sfUser or equivalent
   */
  protected function getUser()
  {
    return $this->getOption('user') ? $this->getOption('user') : $this->getContext()->getUser();
  }

  /**
   * Returns a context object
   *
   * @return sfContext or equivalent
   */
  protected function getContext()
  {
    return $this->getOption('context') ? $this->getOption('context') : sfContext::getInstance();
  }

  /**
   * Returns the i18n version of the string
   * if i18n is activated, or the string itself otherwise
   *
   * @param string $message
   * @param array $option
   *
   * @return string
   */
  protected function __($message, $options = array())
  {
    if (sfConfig::get('sf_i18n') && $this->getOption('lazy_i18n'))
    {
      return $this->getI18n()->__($message, $options, $this->getOption('i18n_catalogue'));
    }
    else
    {
      return strtr($message, $options);
    }
  }
}