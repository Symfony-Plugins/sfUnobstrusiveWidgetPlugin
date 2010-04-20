<?php

/**
 * Pager HTML representation.
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage lib.widget.navigation.pager
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetPager extends sfUoWidget
{
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * pager:                 An instance of sfPager
   *  * route:                 The route to use to generate pager's links
   *  * page_name:             The page variable name (optional)
   *  * max_link_count:        The number max of page links to display
   *
   * @see sfUoWidget->configure()
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('pager');
    $this->addRequiredOption('route');
    $this->addOption('page_name', 'page');
    $this->addOption('max_link_count', 5);
    
    $this->addOption('selected_class', 'selected');
    $this->addOption('template_link', '<a href="%url%">%page%</a>');
    $this->addOption('template_current', '<strong>%page%</strong>');
    
    $this->addOption('template_first_enabled', '<a href="%url%">&lt;&lt;</a>');
    $this->addOption('template_first_disabled', '&lt;&lt;');
    $this->addOption('template_previous_enabled', '<a href="%url%">&lt;</a>');
    $this->addOption('template_previous_disabled', '&lt;');
    
    $this->addOption('template_last_enabled', '<a href="%url%">&gt;&gt;</a>');
    $this->addOption('template_last_disabled', '&gt;&gt;');
    $this->addOption('template_next_enabled', '<a href="%url%">&gt;</a>');
    $this->addOption('template_next_disabled', '&gt;');

    parent::configure($options, $attributes);
  }
  
  /**
   * @return string An HTML tag string
   * @see  render()
   * @todo refactoring
   */
  protected function doRender()
  {
    $pager  = $this->getOption('pager');
    $result = '';
    
    if ($pager->isFirstPage())
    {
      $content    = strtr($this->getOption('template_first_disabled'), array(
        '%url%' => '#'
      ));
      $result    .= sprintf('<li>%s</li>', $content);
    }
    else
    {
      $content    = strtr($this->getOption('template_first_enabled'), array(
        '%url%' => $this->getController()->genUrl(array(
          'sf_route'                    => $this->getOption('route'),
          $this->getOption('page_name') => $pager->getFirstPage(),
        )),
      ));
      $result    .= sprintf('<li>%s</li>', $content);
    }
    
    if ($pager->getPage() == $pager->getPreviousPage())
    {
      $content    = strtr($this->getOption('template_previous_disabled'), array(
        '%url%' => '#'
      ));
      $result    .= sprintf('<li>%s</li>', $content);
    }
    else
    {
      $content    = strtr($this->getOption('template_previous_enabled'), array(
        '%url%' => $this->getController()->genUrl(array(
          'sf_route'                    => $this->getOption('route'),
          $this->getOption('page_name') => $pager->getPreviousPage(),
        )),
      ));
      $result    .= sprintf('<li>%s</li>', $content);
    }

    foreach ($pager->getLinks($this->getOption('max_link_count')) as $page)
    {
      $attributes = '';
      if ($pager->getPage() == $page)
      {
        $content    = strtr($this->getOption('template_current'), array('%page%' => $page, '%url%'  => '#'));
        $attributes = sprintf(' class="%s"', $this->getOption('selected_class'));
      }
      else
      {
        $content = strtr($this->getOption('template_link'), array(
          '%page%' => $page, 
          '%url%'  => $this->getController()->genUrl(array(
            'sf_route'                    => $this->getOption('route'),
            $this->getOption('page_name') => $page,
          )),
        ));
      }

      $result .= sprintf('<li%s>%s</li>', $attributes, $content);
    }
    
    if ($pager->getPage() == $pager->getNextPage())
    {
      $content    = strtr($this->getOption('template_next_disabled'), array(
        '%url%' => '#'
      ));
      $result    .= sprintf('<li>%s</li>', $content);
    }
    else
    {
      $content    = strtr($this->getOption('template_next_enabled'), array(
        '%url%' => $this->getController()->genUrl(array(
          'sf_route'                    => $this->getOption('route'),
          $this->getOption('page_name') => $pager->getNextPage(),
        )),
      ));
      $result    .= sprintf('<li>%s</li>', $content);
    }
    
    if ($pager->isLastPage())
    {
      $content    = strtr($this->getOption('template_last_disabled'), array(
        '%url%' => '#'
      ));
      $result    .= sprintf('<li>%s</li>', $content);
    }
    else
    {
      $content    = strtr($this->getOption('template_last_enabled'), array(
        '%url%' => $this->getController()->genUrl(array(
          'sf_route'                    => $this->getOption('route'),
          $this->getOption('page_name') => $pager->getLastPage(),
        )),
      ));
      $result    .= sprintf('<li>%s</li>', $content);
    }

    return $this->renderContentTag('ul', $result, $this->getRenderAttributes());
  }
}
