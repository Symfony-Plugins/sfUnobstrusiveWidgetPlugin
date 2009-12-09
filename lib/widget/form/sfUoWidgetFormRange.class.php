<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetFormRange represents a range widget.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau <francois.beliveau@my-labz.com.com>
 */
class sfUoWidgetFormRange extends sfUoWidget
{
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * from:        The "from"" widget (required)
   *  * to:          The "to" widget (required)
   *  * template:    The template to use to render the widget
   *                 Available placeholders: %from%, %to%
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('from');
    $this->addRequiredOption('to');

    $this->addOption('template', '<span class="from">from </span>%from%<span class="to"> to </span>%to%');
  }

  /**
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function doRender()
  {
    $name   = $this->getRenderName();
    $value  = $this->getRenderValue();
    $values = array_merge(array('from' => '', 'to' => '', 'is_empty' => ''), is_array($value) ? $value : array());
    
    $widgetFrom       = $this->getOption('from');
    $widgetTo         = $this->getOption('to');
    $isWidgetFromIsUo = is_subclass_of($widgetFrom, 'sfUoWidget');
    $isWidgetToIsUo   = is_subclass_of($widgetFrom, 'sfUoWidget');
    $jsLazyFrom       = $isWidgetFromIsUo ? $widgetFrom->isLazy() : false;
    $jsLazyTo         = $isWidgetToIsUo ? $widgetTo->isLazy() : false;

    if ($jsLazyFrom)
    {
      $widgetFrom->setOption('js_lazy', false);
    }
    if ($jsLazyTo)
    {
      $widgetTo->setOption('js_lazy', false);
    }

    $result = strtr($this->getOption('template'), array(
      '%from%'      => $widgetFrom->render($name.'[from]', $value['from']),
      '%to%'        => $widgetTo->render($name.'[to]', $value['to']),
    ));

    $config = '';
    if ($jsLazyFrom)
    {
      $widgetFrom->setOption('js_lazy', $jsLazyFrom);
      $config .= $widgetFrom->getJsConfig($this->generateId($name.'[from]'));
    }
    if ($jsLazyTo)
    {
      $widgetTo->setOption('js_lazy', $jsLazyTo);
      $config .= $widgetTo->getJsConfig($this->generateId($name.'[to]'));
    }

    return $result.$config;
  }

  /**
   * Gets the stylesheet paths associated with the widget.
   *
   * @return array An array of stylesheet paths
   */
  public function getStylesheets()
  {
    return array_unique(array_merge($this->getOption('from')->getStylesheets(), $this->getOption('to')->getStylesheets()));
  }

  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavaScripts()
  {
    return array_unique(array_merge($this->getOption('from')->getJavaScripts(), $this->getOption('to')->getJavaScripts()));
  }
}
