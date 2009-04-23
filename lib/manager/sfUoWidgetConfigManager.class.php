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
  /**
   * Context references
   */
  protected
    $context    = null,
    $controller = null,
    $request    = null,
    $response   = null;

  /**
   * Constructor
   */
  public function __construct($context)
  {
    $this->context    = $context;
    $this->response   = $context->getResponse();
    $this->request    = $context->getRequest();
    $this->controller = $context->getController();

    $this->configuration = include($context->getConfiguration()->getConfigCache()->checkConfig('config/sfUoWidget.yml'));
  }

  /**
   * Returns global or adapter specific configuration
   *
   * @throws InvalidArgumentException if specified behavior does not have configuration
   *
   * @param  string $adapter If null, global configuration will be returned
   *
   * @return void
   */
  public function getConfiguration($adapter=null)
  {
    if (is_null($adapter))
    {
      return $this->configuration;
    }
    else
    {
      if (!isset($this->configuration[$adapter]))
      {
        throw new InvalidArgumentException('Configuration for sfUoWidget adapter «'.$adapter.'» is not available.');
      }
      return $this->configuration[$adapter];
    }
  }
  
  /**
   * Returns configuration for specific adapter, widget and transformer
   *
   * @throws InvalidArgumentException if specified behavior does not have configuration
   *
   * @param  string $adapter The adapter
   * @param  string $selector The selector
   * @param  string $transformer The transformer
   *
   * @return array
   */
  public function getTransformerConfiguration($adapter, $selector, $transformer)
  {
    try
    {
      $config = $this->getConfiguration($adapter);
    }
    catch(Exception $e)
    {
      throw $e;
    }

    if (!isset($config[$selector]))
    {
        throw new InvalidArgumentException('Configuration for sfUoWidget selector «'.$selector.'» is not available for «'.$adapter.'» adapter.');
    }
    if (!isset($config[$selector][$transformer]))
    {
        throw new InvalidArgumentException('Configuration for sfUoWidget transformer «'.$transformer.'» is not available for «'.$adapter.'» adapter and «'.$selector.'» selector.');
    }
    
    return $config[$selector][$transformer];
  }

  /**
   * Returns javascripts for specific adapter, widget and transformer configuration
   *
   * @throws InvalidArgumentException if specified behavior does not have configuration
   *
   * @param  string $adapter The adapter
   * @param  string $selector The selector
   * @param  string $transformer The transformer
   *
   * @return array
   */
  public function getJavascripts($adapter, $selector, $transformer)
  {
    try
    {
      $config = $this->getTransformerConfiguration($adapter, $selector, $transformer);
    }
    catch(Exception $e)
    {
      throw $e;
    }

    return isset($config['js_files']) ? $config['js_files'] : array();
  }
  
  /**
   * Returns stylesheets for specific adapter, widget, transformer and skin configuration
   *
   * @throws InvalidArgumentException if specified behavior does not have configuration
   *
   * @param  string $adapter The adapter
   * @param  string $selector The selector
   * @param  string $transformer The transformer
   * @param  string $skin The skin
   *
   * @return array
   */
  public function getStylesheets($adapter, $selector, $transformer, $skin)
  {
    try
    {
      $config = $this->getTransformerConfiguration($adapter, $selector, $transformer);
    }
    catch(Exception $e)
    {
      throw $e;
    }
    
    if (!isset($config['css_files']))
    {
      return array();
    }
    if (!isset($config['css_files'][$skin]))
    {
      throw new InvalidArgumentException('Configuration for sfUoWidget skin «'.$skin.'» is not available for «'.$adapter.'» adapter, «'.$selector.'» selector and «'.$transformer.'» transformer.');
    }

    return $config['css_files'][$skin];
  }
  
  /**
   * Returns compatibilities for specific adapter, widget and transformer configuration
   *
   * @throws InvalidArgumentException if specified behavior does not have configuration
   *
   * @param  string $adapter The adapter
   * @param  string $selector The selector
   * @param  string $transformer The transformer
   *
   * @return array
   */
  public function getCompatibilities($adapter, $selector, $transformer)
  {
    try
    {
      $config = $this->getTransformerConfiguration($adapter, $selector, $transformer);
    }
    catch(Exception $e)
    {
      throw $e;
    }

    return isset($config['compatibilities']) ? $config['compatibilities'] : array();
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
   * @return void
   */
  public function checkTransformersCompatibilities($adapter, $selector, Array $transformers)
  {
    try
    {
      foreach ($transformers as $transformer)
      {
        $compatibilities = $this->getCompatibilities($adapter, $selector, $transformer);
        foreach ($transformers as $jsTransformer)
        {
          if ($transformer != $jsTransformer)
          {
            if (!in_array($jsTransformer, $compatibilities))
            {
              throw new InvalidArgumentException('"'.$jsTransformer.'" transformer is incompatible with "'.$transformer.'" transformer');
            }
          }
        }
      }
    }
    catch(Exception $e)
    {
      throw $e;
    }
  }
  
  /**
   * Returns all javascripts
   *
   * @param  string $adapter The adapter
   *
   * @return array
   */
  public function getAllJavascripts($adapter = null)
  {
    $config = $this->getConfiguration($adapter);
    return $this->getAllConfig('js_files', $config);
  }
  
  /**
   * Returns all stylesheets
   *
   * @param  string $adapter The adapter
   * @param  string $skin The skin
   *
   * @return array
   */
  public function getAllStylesheets($adapter = null, $skin = null)
  {
    $config = $this->getConfiguration($adapter);
    $result = $this->getAllConfig('css_files', $config);
    
    if (!is_null($skin))
    {
      foreach ($result as $key=>$value)
      {
        if ($key != $skin)
        {
          unset($result[$key]);
        }
      }
    }

    return $result;
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
  public function haveToSetsInWindowOnLoad($adapter, $selector, $transformer)
  {
    $config = $this->getTransformerConfiguration($adapter, $selector, $transformer);
    return isset($config['window_onload']) ? $config['window_onload'] : false;
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
  
  /**
   * ArrayAccess: unset
   */
  protected function getAllConfig($configName, Array $config, $result = array())
  {
    foreach ($config as $key=>$value)
    {
      if ($configName == $key)
      {
        if (is_array($value))
        {
          foreach ($value as $v)
          {
            $result[] = $v;
          }
        }
      }
      else if(is_array($value))
      {
        $result = $this->getAllConfig($configName, $value, $result);
      }
    }
    
    return $result;
  }
}