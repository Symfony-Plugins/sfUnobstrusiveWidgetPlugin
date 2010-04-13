<?php

/**
 * sfUoWidgetFormPropelCheckList
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage lib.widget.orm.propel.form
 * @author     Fran�ois B�liveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetFormPropelCheckList extends sfUoWidgetPropelList
{
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * multiple:               true if the list must allow multiple selections
   *
   * Available transformers:
   *
   *  * nested_check
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfUoWidget
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
  
    $this->addOption('multiple', true);
  }
  
  /**
   * Gets the JavaScript selector.
   *
   * @return string A JS selector
   */
  protected function getJsSelector()
  {
    return 'uo_widget_form_list';
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
    $id                 = $this->getId().'_'.$key;
    $name               = $this->getOption('multiple') ? $this->getRenderName().'['.$key.']' : $this->getRenderName();
    $renderValue        = $this->getRenderValue();
    $attributes         = array('id'=>$id, 'name'=>$name, 'value'=>$key);
    $attributes['type'] = $this->getOption('multiple') ? 'checkbox' : 'radio';
    if (
      !empty($renderValue) 
      && $renderValue !== false 
      && ($key == $renderValue || (is_array($renderValue) && in_array($key, $renderValue)))
    )
    {
      $attributes['checked'] = 'checked';
    }
  
    if (is_array($value))
    {
      if (array_key_exists('label', $value))
      {
        $value['label'] = $this->renderTag('input', $attributes).$this->renderContentTag('label', $value['label'], array('for'=>$id));
      }
    }
    else
    {
      $value = $this->renderTag('input', $attributes).$this->renderContentTag('label', $value, array('for'=>$id));
    }
    
    return parent::getItemContent($key, $value);
  }
}