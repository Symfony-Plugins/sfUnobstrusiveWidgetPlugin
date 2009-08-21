<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetList
 * List widget rend a simple or a nested list.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetList extends sfUoWidget
{
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * choices:               An array of possible choices (required)
   *  * list_type:             List type ("ul" by default)
   *  * root:                  Root name (null by default)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfUoWidget
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
  
    $this->addRequiredOption('choices');
    $this->addOption('list_type', 'ul');
    $this->addOption('root_label', '');
    $this->addOption('add_root', false);
  }
  
  /**
   * @return string An HTML tag string
   *
   * @see render()
   */
  protected function doRender()
  {
    $choices = $this->getOption('choices');
    if ($choices instanceof sfCallable)
    {
      $choices = $choices->call();
    }

    $itemAttributes = array();
    if ($this->getOption('add_root'))
    {
      $choices                  = array($this->getOption('root_name') => $choices);
      $itemAttributes['class']  = 'root';
    }
    
    return $this->renderItemContainer($this->recursiveRender($choices, $itemAttributes, false, true), $this->getRenderAttributes());
  }
 
  /**
   * Gets the JavaScript selector.
   *
   * @return string A JS selector
   */
  protected function getJsSelector()
  {
    return 'uo_widget_list';
  }

  /**
   * Renders the widget content.
   *
   * @param  array $choices         An array of possible choices
   * @param  array $attributes      An array of attributes
   * @param  boolean  $parent      The flag to know if parent tag should be create or not
   * @param  boolean  $root        The flag to know if root or not
   *
   * @return string
   */
  protected function recursiveRender(array $choices, $attributes = array(), $parent = false, $root = false)
  {
    $result = '';
    foreach ($choices as $key => $value)
    {
      if (is_array($value) && isset($value['label'])) 
      { 
        $value['label'] = $this->__($value['label']); 
      } 
      elseif (!is_array($value)) 
      { 
        $value = $this->__($value); 
      }

      $attr = array_merge($attributes, (is_array($value) && array_key_exists('attributes', $value)) ? $value['attributes'] : array());
      $result .= $this->renderItem($key, $this->getItemContent($key, $value), $attr);
    }

    if ($parent)
    {
      $result = $this->renderItemContainer($result);
    }

    return $result;
  }
  
  /**
   * Renders the widget content.
   *
   * @param  array $choices         An array of possible choices
   * @param  array $attributes      An array of attributes
   * @param  boolean  $parent      The flag to know if parent tag should be create or not
   * @param  boolean  $root        The flag to know if root or not
   *
   * @return string
   */
  protected function renderItemContainer($content, $attributes = array())
  {
    return empty($content) ? '' : $this->renderContentTag($this->getOption('list_type'), $content, $attributes);
  }
  
  /**
   * Return an item.
   *
   * @param  string $key
   * @param  mixed $content
   * @param  array $attributes
   *
   * @return string
   */
  protected function renderItem($key, $content, $attributes = array())
  {
    return empty($content) ? '' : $this->renderContentTag('li', $content, $attributes);
  }

  /**
   * Return an item content.
   *
   * @param  string $key
   * @param  mixed $value
   *
   * @return string
   */
  protected function getItemContent($key, $value)
  {
    if (is_array($value))
    {
      if (array_key_exists('label', $value))
      {
        $result = $value['label'];
        if (array_key_exists('contents', $value))
        {
          $result .= $this->recursiveRender($value['contents'], array(), true);
        }
      }
      else
      {
        $result  = $key;
        $result .= $this->recursiveRender($value, array(), true);
      }
    }
    else
    {
      $result = $value;
    }
    
    return $result;
  }
  
  /**
   * Clone funtion.
   */
  public function __clone()
  {
    if ($this->getOption('choices') instanceof sfCallable)
    {
      $callable = $this->getOption('choices')->getCallable();
      if (is_array($callable))
      {
        $callable[0] = $this;
        $this->setOption('choices', new sfCallable($callable));
      }
    }
  }
}
