<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetContainersList
 * Container widget render a simpe list with a title on each items.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetContainersList extends sfUoWidgetList
{
  /**
   * Gets the JavaScript selector.
   *
   * @return string A JS selector
   */
  protected function getJsSelector()
  {
    return 'uo_widget_containers_list';
  }
  
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * title_type:            An array of possible choices ("h3" by default)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfUoWidget
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('title_type', 'h3');
  }

  protected function getItemContent($key, $value)
  {
    $result  = $this->renderContentTag($this->getOption('title_type'), $key, array('class' => 'uo_widget_containers_list_title'));
    $result .= $this->renderContentTag('div', $value, array('class' => 'uo_widget_containers_list_content'));
    
    return $result;
  }
}