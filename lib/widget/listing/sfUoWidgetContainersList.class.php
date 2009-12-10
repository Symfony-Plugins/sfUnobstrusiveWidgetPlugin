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
   *  * title_type:            The html title element to use to generate each container's title ("h3" by default)
   *  * class_title:           The title's classname ("uo_widget_containers_list_title" by default)
   *  * class_container:   The container's classname ("uo_widget_containers_list_content" by default)
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
    $this->addOption('class_title', 'uo_widget_containers_list_title');
    $this->addOption('class_container', 'uo_widget_containers_list_content');
  }

  protected function getItemContent($key, $value)
  {
    $result  = $this->renderContentTag($this->getOption('title_type'), $key, array('class' => $this->getOption('class_title')));
    $result .= $this->renderContentTag('div', $value, array('class' => $this->getOption('class_container')));
    
    return $result;
  }
}