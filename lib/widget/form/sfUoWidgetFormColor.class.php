<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetFormColor represents a color widget.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau
 */
class sfUoWidgetFormColor extends sfUoWidgetFormInput
{
  protected
    $textSize = array(
      'hexa' => 2,
      'rgb'  => 3,
    );

  /**
   * @see sfWidget
   */
  public function __construct($options = array(), $attributes = array())
  {
    if (array_key_exists('mode', $options) && !in_array($options['mode'], array_keys(self::$textSize)))
    {
      throw new InvalidArgumentException('Invalid value for option "mode"');
    }

    parent::__construct($options, $attributes);
  }
    
  /**
   * Return JS config id.
   *
   * @return string The JS id
   */
  public function getJsId($id)
  {
    return parent::getJsId($id).'_blue';
  }

  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * as_text:   Render widget as input text (false by default)
   *  * can_be_empty:   Whether the widget accept an empty value (true by default)
   *  * mode:   hexa or rgb mode
   *  * empty_values:   An array of values to use for the empty value (empty string for red, green, and blue by default)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormDate
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    
    $this->addOption('as_text', false);
    $this->addOption('can_be_empty', true);
    $this->addOption('mode', 'hexa');
    $this->addOption('empty_values', array('red' => '', 'green' => '', 'blue' => ''));
  }
  
  /**
   * @return string An HTML tag string
   *
   * @see render()
   */
  protected function doRender()
  {
    $value   = $this->getValue($this->getRenderValue());
    $result  = '';
    
    foreach (array('red', 'green', 'blue') as $colorKey)
    {
      $result .= $this->getWidget($this->getRenderName(), $colorKey, $this->getRenderAttributes(), $value);
    }

    return $result;
  }
  
  protected function getWidget($name, $key, $attributes, $value)
  {
    $emptyValues = $this->getOption('empty_values');
    
    if ($key != 'blue')
    {
      $class                = str_replace($this->getJsClass(), '', isset($attributes['class']) ? $attributes['class'] : null);
      $attributes['class']  = $class;
    }

    $attributes = $this->addAttribute($attributes, 'class', $key);

    if ($this->getOption('as_text'))
    {
      $widget = new sfWidgetFormInput(array(), array_merge($attributes, array('maxlength' => self::$textSize[$this->getOption('mode')], 'size' => self::$textSize[$this->getOption('mode')])));
    }
    else
    {
      $widget = new sfWidgetFormSelect(array('choices' => $this->getChoices($emptyValues[$key])), $attributes);
    }

    return $widget->render($name.'['.$key.']', $value[$key]);
  }

  protected function getValue($value)
  {
    $default = array('red' => null, 'green' => null, 'blue' => null);
    if (is_array($value))
    {
      $result = array_merge($default, $value);
    }
    else
    {
      $length = strlen($value);
      if (6 == $length || 3 == $length)
      {
        list($r, $g, $b)  = str_split($value, $length / 3);
        $default['red']   = $r;
        $default['green'] = $g;
        $default['blue']  = $b;
      }
      else
      {
        $result = $default;
      }
    }

    return $result;
  }
  
  /**
   * Gets the JavaScript selector.
   *
   * @return string A JS selector
   */
  protected function getJsSelector()
  {
    return 'uo_widget_form_color';
  }
  
  protected function getChoices($emptyValue = '')
  {
    $method  = 'getChoice'.ucfirst($this->getOption('mode'));
    $choices = $this->getOption('can_be_empty') ? array('' => $emptyValue) : array();
    for($i=0; $i<256; $i++)
    {
      $value           = $this->$method($i);
      if (!empty($value))
      {
        $choices[$value] = $value;
      }
    }
    
    asort($choices);
    
    return $choices;
  }
  
  protected function getChoiceHexa($value)
  {
    $value = dechex($value);
    if (strlen($value) < 2)
    {
      $value = '0'.$value;
    }

    return $value;
  }
  
  protected function getChoiceRgb($value)
  {
    return $value;
  }
}