<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetConfigManager
 * Config manager for JS transformers and CSS skins.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetConfigManager implements ArrayAccess
{
  protected
    $context       = null,
    $configuration = array();

  /**
   * Constructor
   */
  public function __construct(sfContext $context)
  {
    $this->context = $context;
    $this->configuration = include($this->context->getConfiguration()->getConfigCache()->checkConfig('config/sfUoWidget.yml'));
  }
  
  /**
   * Returns context
   *
   * @return sfContext
   */
  public function getContext()
  {
    return $this->context;
  }

  /**
   * Returns all configuration
   *
   * @return array
   */
  public function getAllConfiguration()
  {
    return $this->configuration;
  }

  /**
   * Returns default adapter
   *
   * @return string
   */
  public function getDefaultAdapter()
  {
    return $this->configuration['default_adapter'];
  }
  
  /**
   * Returns default adapter
   *
   * @return string
   */
  public function isInLazyModeByDefault()
  {
    return $this->configuration['lazy_mode'];
  }
  
  /**
   * Returns availables adapter
   *
   * @return array
   */
  public function getAvailableAdapters()
  {
    return array_keys($this->configuration['adapters']);
  }
  
  /**
   * Returns if an adapter is available
   *
   * @param  string
   *
   * @return boolean
   */
  public function isAvailableAdapter($adapter)
  {
    return in_array($adapter, $this->getAvailableAdapters());
  }

  /**
   * Returns availables themes
   *
   * @return array
   */
  public function getAvailableThemes()
  {
    return array_keys($this->configuration['themes']);
  }
  
  /**
   * Returns if a theme is available
   *
   * @param  string
   *
   * @return boolean
   */
  public function isAvailableTheme($theme)
  {
    return in_array($theme, $this->getAvailableThemes());
  }

  /**
   * Returns adapter specific configuration
   *
   * @throws InvalidArgumentException if specified adapter does not have configuration
   *
   * @param  string
   *
   * @return array
   */
  public function getAdapterConfiguration($adapter)
  {
    if (!$this->isAvailableAdapter($adapter))
    {
      throw new InvalidArgumentException('Adapter «'.$adapter.'» is not available.');
    }
    return $this->configuration['adapters'][$adapter];
  }
  
  /**
   * Returns adapter specific configuration
   *
   * @throws InvalidArgumentException if specified adapter does not have configuration
   *
   * @param  string
   *
   * @return array
   */
  public function getAdapterTemplateConfiguration($adapter)
  {
    try
    {
      $config = $this->getAdapterConfiguration($adapter);
    }
    catch(Exception $e)
    {
      throw $e;
    }

    return $config['templates'];
  }
  
  /**
   * Returns adapter specific configuration
   *
   * @throws InvalidArgumentException if specified adapter does not have configuration
   *
   * @param  string
   *
   * @return array
   */
  public function getAdapterTemplate($adapter, $template)
  {
    try
    {
      $config = $this->getAdapterTemplateConfiguration($adapter);
    }
    catch(Exception $e)
    {
      throw $e;
    }
    
    if (!array_key_exists($template, $config))
    {
      throw new InvalidArgumentException('Template «'.$template.'» is not available for adapter «'.$adapter.'».');
    }

    return $config[$template];
  }
  
  /**
   * Returns adapter specific configuration
   *
   * @throws InvalidArgumentException if specified adapter does not have configuration
   *
   * @param  string
   *
   * @return array
   */
  public function getAdapterTheme($adapter)
  {
    try
    {
      $config = $this->getAdapterConfiguration($adapter);
    }
    catch(Exception $e)
    {
      throw $e;
    }

    return $config['theme'];
  }
  
  /**
   * Returns selector configuration for a specific adapter
   *
   * @throws InvalidArgumentException if specified adapter does not have configuration
   *
   * @param  string
   *
   * @return array
   */
  public function getSelectorConfiguration($adapter, $selector)
  {
    try
    {
      $config = $this->getAdapterConfiguration($adapter);
    }
    catch(Exception $e)
    {
      throw $e;
    }

    if (!array_key_exists($selector, $config['packages']))
    {
      throw new InvalidArgumentException('Selector «'.$selector.'» is not available for adapter «'.$adapter.'».');
    }

    return $config['packages'][$selector];
  }
  
  /**
   * Returns transformer specific configuration
   *
   * @throws InvalidArgumentException if specified transormer does not exists
   *
   * @param  string
   * @param  string
   * @param  string
   *
   * @return array
   */
  public function getTransformerConfiguration($adapter, $selector, $tranformer)
  {
    try
    {
      $config = $this->getSelectorConfiguration($adapter, $selector);
    }
    catch(Exception $e)
    {
      return $this->getSelectorConfiguration($adapter, $tranformer);
    }
    
    if (!array_key_exists($tranformer, $config))
    {
      throw new InvalidArgumentException('Transformer «'.$tranformer.'» is not available for adapter «'.$adapter.'» and selector «'.$selector.'».');
    }

    return $config[$tranformer];
  }
  
  /**
   * Returns transformer specific configuration
   *
   * @throws InvalidArgumentException if specified transormer does not exists
   *
   * @param  string
   * @param  string
   * @param  string
   *
   * @return array
   */
  public function getTransformerJavascripts($adapter, $selector, $tranformer)
  {
    try
    {
      $config = $this->getTransformerConfiguration($adapter, $selector, $tranformer);
    }
    catch(Exception $e)
    {
      throw $e;
    }

    return array_key_exists('js_files', $config) ? $config['js_files'] : array();
  }
  
  /**
   * Returns transformer specific configuration
   *
   * @throws InvalidArgumentException if specified transormer does not exists
   *
   * @param  string
   * @param  string
   * @param  string
   *
   * @return array
   */
  public function getTransformerStylesheets($adapter, $selector, $tranformer)
  {
    try
    {
      $config = $this->getTransformerConfiguration($adapter, $selector, $tranformer);
    }
    catch(Exception $e)
    {
      throw $e;
    }

    return array_key_exists('css_files', $config) ? $config['css_files'] : array();
  }
  
  /**
   * Return true if an adapter's transformer have to be call from window on load, false otherwise
   *
   * @param  string $adapter The adapter
   * @param  string $selector The selector
   * @param  string $transformer The transformer
   *
   * @return boolean
   */
  public function getTransformerTemplate($adapter, $selector, $transformer)
  {
    try
    {
      $config = $this->getTransformerConfiguration($adapter, $selector, $transformer);
      $result = $this->getAdapterTemplate($adapter, array_key_exists('template', $config) ? $config['template'] : 'default');
    }
    catch(Exception $e)
    {
      $result = $this->getAdapterTemplate($adapter, 'default');
    }

    return $result;
  }
  
  /**
   * Returns transformer specific configuration
   *
   * @throws InvalidArgumentException if specified transormer does not exists
   *
   * @param  string
   * @param  string
   * @param  string
   *
   * @return array
   */
  public function getTransformerCompatibilities($adapter, $selector, $tranformer)
  {
    try
    {
      $config = $this->getTransformerConfiguration($adapter, $selector, $tranformer);
    }
    catch(Exception $e)
    {
      throw $e;
    }

    return array_key_exists('compatibilities', $config) ? $config['compatibilities'] : array();
  }
  
  /**
   * Check compatibilities for specific adapter, widget and transformer configuration
   *
   * @throws InvalidArgumentException if invalid
   *
   * @param  string $adapter The adapter
   * @param  string $selector The selector
   * @param  aray $transformer The transformers
   *
   * @return boolean
   */
  public function checkTransformersCompatibilities($adapter, $selector, array $transformers)
  {
    try
    {
      foreach ($transformers as $transformer)
      {
        $compatibilities = $this->getTransformerCompatibilities($adapter, $selector, $transformer);
        foreach ($transformers as $jsTransformer)
        {
          if ($transformer != $jsTransformer)
          {
            if (!in_array($jsTransformer, $compatibilities))
            {
              throw new InvalidArgumentException('«'.$jsTransformer.'» transformer is incompatible with «'.$transformer.'» transformer');
            }
          }
        }
      }
    }
    catch(Exception $e)
    {
      throw $e;
    }
    
    return true;
  }
  
  /**
   * Returns theme specific configuration
   *
   * @throws InvalidArgumentException if specified theme does not have configuration
   *
   * @param  string
   *
   * @return array
   */
  public function getThemeConfiguration($theme)
  {
    if (!$this->isAvailableTheme($theme))
    {
      throw new InvalidArgumentException('Theme «'.$theme.'» is not available.');
    }
    return $this->configuration['themes'][$theme];
  }
  
  /**
   * Returns transformer specific configuration
   *
   * @throws InvalidArgumentException if specified transormer does not exists
   *
   * @param  string
   *
   * @return array
   */
  public function getThemeStylesheets($theme)
  {
    try
    {
      $config = $this->getThemeConfiguration($theme);
    }
    catch(Exception $e)
    {
      throw $e;
    }

    return array_key_exists('css_files', $config) ? $config['css_files'] : array();
  }

  /**
   * ArrayAccess: isset
   */
  public function offsetExists($offset)
  {
    return isset($this->configuration[$offset]);
  }

  /**
   * ArrayAccess: getter
   */
  public function offsetGet($offset)
  {
    return $this->configuration[$offset];
  }

  /**
   * ArrayAccess: setter
   */
  public function offsetSet($offset, $value)
  {
    throw new LogicException('Cannot use array access of widget js transformer manager in write mode.');
  }

  /**
   * ArrayAccess: unset
   */
  public function offsetUnset($offset)
  {
    throw new LogicException('Cannot use array access of widget js transformer manager in write mode.');
  }
}