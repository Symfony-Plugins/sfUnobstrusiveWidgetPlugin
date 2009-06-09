<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Loader mock.
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage test
 * @author     François Béliveau
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