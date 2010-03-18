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
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * controller:            Controller object to generate url (null by default)
   *  * active:                The active url (null by default)
   *
   * @see sfUoWidget->configure()
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('active', null);
    $this->addOption('active_class', 'active');
    $this->addOption('active_onlink', true);
    $this->addOption('active_tag', 'strong');
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
    if (is_array($value) && array_key_exists('label', $value))
    {
      if ($this->hasUrl($value))
      {
        $options = array('href' => $this->getUrl($value));
        if ($this->getOption('active') == $this->getUrl($value))
        {
          if ($this->getOption('active_onlink'))
          {
            $options['class'] = 'active';
          }
          $value['label']   = $this->renderContentTag($this->getOption('active_tag'), $value['label'], array());
        }
        $value['label'] = $this->renderContentTag('a', $value['label'], $options);
        unset($value['url']);
      }
    }
    
    return parent::getItemContent($key, $value);
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
    if (!$this->getOption('active_onlink') && strpos($content, $this->getOption('active_tag')))
    {
      $attributes['class'] = isset($attributes['class']) ? sprinft('%s %s', $attributes['class'], $this->getOption('active_class')) : $this->getOption('active_class');
    }
    return empty($content) ? '' : $this->renderContentTag('li', $content, $attributes);
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
        $result = $this->getController()->genUrl($values['route'], array_key_exists('absolute', $values) && $values['absolute']);
      }
    }
    
    return $result ? $result : '#';
  }
}
