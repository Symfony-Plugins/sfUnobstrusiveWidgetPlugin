<?php

/**
 * Loader mock.
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage lib.test
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoLoaderMock
{
  protected
    $loaded = array();

  public function loadTransformers($adapter, $selector, array $transformers)
  {
    foreach ($transformers as $transformer)
    {
      $this->loaded[$adapter][$selector][$transformer] = true;
    }
  }
  
  public function getLoaded()
  {
    return $this->loaded;
  }
}