<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetFormDate represents a date widget.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
class sfUoWidgetFormDate extends sfUoWidget
{
  static protected
    $textSize = array(
      'day'   => 2,
      'month' => 2,
      'year'  => 4,
    );

  /**
   * Return JS config id.
   *
   * @return string The JS id
   */
  public function getJsId($id)
  {
    return parent::getJsId($id).'_'.$this->getLastSelectName();
  }

  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * year_as_text:   Render year widget as input text (false by default)
   *  * month_as_text:  Render month widget as input text (false by default)
   *  * day_as_text:    Render day widget as input text (false by default)
   *  * format:         The date format string (%month%/%day%/%year% by default)
   *  * years:          An array of years for the year select tag (optional)
   *                    Be careful that the keys must be the years, and the values what will be displayed to the user
   *  * months:         An array of months for the month select tag (optional)
   *  * days:           An array of days for the day select tag (optional)
   *  * can_be_empty:   Whether the widget accept an empty value (true by default)
   *  * empty_values:   An array of values to use for the empty value (empty string for year, month, and day by default)
   
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormDate
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    
    $this->addOption('year_as_text', false);
    $this->addOption('month_as_text', false);
    $this->addOption('day_as_text', false);
    $this->addOption('can_be_empty', true);
    $this->addOption('empty_values', array('year' => '', 'month' => '', 'day' => ''));    
    $this->addOption('month_format', 'number');
    
    $culture     = isset($this->options['culture']) ? $this->options['culture'] : $this->getUser()->getCulture();
    $monthFormat = isset($this->options['month_format']) ? $this->options['month_format'] : 'name';
    $months      = isset($this->options['months']) ? $this->options['months'] : parent::generateTwoCharsRange(1, 12);
    
    $this->addOption('format', $this->getDateFormat($culture));
    $this->addOption('days', parent::generateTwoCharsRange(1, 31));
    $this->addOption('months', $this->getMonthFormat($culture, $monthFormat, $months));
    $years = range(date('Y') - 5, date('Y') + 5);
    $this->addOption('years', array_combine($years, $years));
  }
  
  /**
   * @return string An HTML tag string
   *
   * @see render()
   */
  protected function doRender()
  {
    $lastSelect   = $this->getLastSelectName();
    $value        = $this->getValue($this->getRenderValue());
    $date         = array();
    $keys         = array('day', 'month', 'year');
    
    foreach ($keys as $key)
    {
      $date['%'.$key.'%'] = $this->getWidget($this->getRenderName(), $key, $this->getRenderAttributes(), $value, $lastSelect);
    }

    return strtr($this->getOption('format'), $date);
  }

  protected function getWidget($name, $key, $attributes, $value, $lastSelect)
  {
    $emptyValues = $this->getOption('empty_values');
    
    if ($key != $lastSelect)
    {
      $class                = str_replace($this->getJsClass(), '', isset($attributes['class']) ? $attributes['class'] : null);
      $attributes['class']  = $class;
    }
    $attributes = $this->addAttribute($attributes, 'class', $key);

    $choices = $this->getOption('can_be_empty') ? array('' => $emptyValues[$key]) + $this->getOption($key.'s') : $this->getOption($key.'s');
    if ($this->getOption($key.'_as_text'))
    {
      $widget  = new sfWidgetFormInput(array(), array_merge($attributes, array('maxlength'=>self::$textSize[$key], 'size'=>self::$textSize[$key])));
    }
    else
    {
      $widget  = new sfWidgetFormSelect(array('choices' => $choices), $attributes);
    }

    return $widget->render($name.'['.$key.']', $value[$key]);
  }

  protected function getLastSelectName()
  {
    $format = explode('%', $this->getOption('format'));
    for ($i = count($format)-1; $i>=0; $i--)
    {
      if (!empty($format[$i]))
      {
        return $format[$i];
      }
    }

    return null;
  }

  protected function getValue($value)
  {
    // convert value to an array
    $default = array('year' => null, 'month' => null, 'day' => null);
    if (is_array($value))
    {
      $result = array_merge($default, $value);
    }
    else
    {
      $value = (string) $value == (string) (integer) $value ? (integer) $value : strtotime($value);
      if (false === $value)
      {
        $result = $default;
      }
      else
      {
        $result = array('year' => date('Y', $value), 'month' => date('n', $value), 'day' => date('j', $value));
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
    return 'uo_widget_form_date';
  }
  
  protected function getMonthFormat($culture, $monthFormat, array $months)
  {
    switch ($monthFormat)
    {
      case 'name':
        return array_combine($months, sfDateTimeFormatInfo::getInstance($culture)->getMonthNames());
      case 'short_name':
        return array_combine($months, sfDateTimeFormatInfo::getInstance($culture)->getAbbreviatedMonthNames());
      case 'number':
        return $months;
      default:
        throw new InvalidArgumentException(sprintf('The month format "%s" is invalid.', $monthFormat));
    }
  }

  protected function getDateFormat($culture)
  {
    $dateFormat = sfDateTimeFormatInfo::getInstance($culture)->getShortDatePattern();

    if (false === ($dayPos = stripos($dateFormat, 'd')) || false === ($monthPos = stripos($dateFormat, 'm')) || false === ($yearPos = stripos($dateFormat, 'y')))
    {
      return $this->getOption('format');
    }

    return strtr($dateFormat, array(
      substr($dateFormat, $dayPos,   strripos($dateFormat, 'd') - $dayPos + 1)   => '%day%',
      substr($dateFormat, $monthPos, strripos($dateFormat, 'm') - $monthPos + 1) => '%month%',
      substr($dateFormat, $yearPos,  strripos($dateFormat, 'y') - $yearPos + 1)  => '%year%',
    ));
  }
}