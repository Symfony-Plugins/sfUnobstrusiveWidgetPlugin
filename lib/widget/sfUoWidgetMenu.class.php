<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetMenu
 * Menu widget rend a list with link.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetMenu extends sfUoWidgetList
{
  protected
    $controller = null;
  
  /**
   * Configures the current widget.
   *
   * @see sfUoWidget->configure()
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    
    $this->addOption('controller', null);
  }
  
  /**
   * @return string An HTML tag string
   *
   * @see render()
   */
  protected function doRender()
  {
    $this->controller = $this->getOption('controller') ? $this->getOption('controller') : sfContext::getInstance()->getController();

    return parent::doRender();
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
    if (isset($value['label']))
    {
      $value['label'] = $this->hasUrl($value) ? $this->renderContentTag('a', $value['label'], array('href' => $this->getUrl($value))) : $value['label'];
      unset($value['url']);
    }
    
    return parent::getItemContent($key, $value);
  }

  /**
   * Is values contain an url ?
   *
   * @param  array $values       The values
   *
   * @return boolean
   */
  protected function hasUrl(Array $values)
  {
    return array_key_exists('url', $values) || array_key_exists('route', $values);
  }

  /**
   * Return an url.
   *
   * @param  array $values       The values
   *
   * @return string
   */
  protected function getUrl(Array $values)
  {
    $result = '';
    if ($this->hasUrl($values))
    {
      if (array_key_exists('url', $values))
      {
        $result = $values['url'];
      }
      else
      {
        $result = $this->controller->genUrl($values['route'], array_key_exists('absolute', $values) && $values['absolute']);
      }
    }
    
    return $result ? $result : '#';
  }
}