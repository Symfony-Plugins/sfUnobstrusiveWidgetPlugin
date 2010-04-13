<?php

/**
 * Controller mock.
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage lib.test
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoControllerMock
{
  public function genUrl($route)
  {
    return str_replace(array('?', '&', '='), '/', $route);
  }
}