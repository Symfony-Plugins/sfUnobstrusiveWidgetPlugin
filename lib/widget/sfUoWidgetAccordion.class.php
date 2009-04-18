<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetAccordion
 * Accordion widget render a simpe list with a title on each items.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetAccordion extends sfUoWidgetList
{
  /**
   * @see sfUoWidget
   */
  public function __construct($options = array(), $attributes = array())
  {
    $options['js_transformer'] = 'accordion';

    parent::__construct($options, $attributes);
  }

  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * title_type:            An array of possible choices ("h3" by default)
   *
   * Available transformers:
   *
   *  * accordion
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
    $result  = $this->renderContentTag($this->getOption('title_type'), $key, array('class' => 'uo_widget_accordion_title'));
    $result .= $this->renderContentTag('div', $value, array('class' => 'uo_widget_accordion_content'));
    
    return $result;
  }
}