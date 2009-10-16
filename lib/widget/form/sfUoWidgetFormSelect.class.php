<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetFormSelect
 * Represents a select HTML tag where you can select multiple values.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau
 */
class sfUoWidgetFormSelect extends sfUoWidget
{
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * choices:  An array of possible choices (required)
   *  * multiple: true if the select tag must allow multiple selections
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
    $this->addOption('multiple', false);
  }

  /**
   * @return string An HTML tag string
   *
   * @see render()
   */
  protected function doRender()
  {
    $attributes = $this->getRenderAttributes();
    if ($this->getOption('multiple'))
    {
      $attributes['multiple'] = 'multiple';
      if ('[]' != substr($this->getRenderName(), -2))
      {
        $this->setRenderName($this->getRenderName().'[]');
      }
    }

    $choices = $this->getOption('choices');
    if ($choices instanceof sfCallable)
    {
      $choices = $choices->call();
    }

    return $this->renderContentTag('select', "\n".implode("\n", $this->getOptionsForSelect($this->getRenderValue(), $choices))."\n", array_merge(array('name' => $this->getRenderName()), $attributes));
  }
  
  /**
   * Gets the JavaScript selector.
   *
   * @return string A JS selector
   */
  protected function getJsSelector()
  {
    return $this->getOption('multiple') ? 'uo_widget_form_select_many' : 'uo_widget_form_select';
  }

  /**
   * Returns an array of option tags for the given choices
   *
   * @param  string $value    The selected value
   * @param  array  $choices  An array of choices
   *
   * @return array  An array of option tags
   */
  protected function getOptionsForSelect($value, $choices)
  {
    $mainAttributes = $this->attributes;
    $this->attributes = array();

    $options = array();
    foreach ($choices as $key => $option)
    {
      if (is_array($option) && !array_key_exists('label', $option))
      {
        $options[] = $this->renderContentTag('optgroup', implode("\n", $this->getOptionsForSelect($value, $option)), array('label' => self::escapeOnce($key)));
      }
      else
      {
        $attributes = array('value' => self::escapeOnce($key));
      
        if (is_array($option))
        {
          if (array_key_exists('attributes', $option))
          {
            $attributes = array_merge($option['attributes'], $attributes);
          }
          
          if (array_key_exists('label', $option))
          {
            $option = $option['label'];
          }
        }
      
        if ((is_array($value) && in_array(strval($key), $value)) || strval($key) == strval($value))
        {
          $attributes['selected'] = 'selected';
        }

        $options[] = $this->renderContentTag('option', $this->__(self::escapeOnce($option)), $attributes);
      }
    }

    $this->attributes = $mainAttributes;

    return $options;
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