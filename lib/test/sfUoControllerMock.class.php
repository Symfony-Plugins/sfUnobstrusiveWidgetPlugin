<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Controller mock.
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage test
 * @author     François Béliveau
 */
class sfUoControllerMock
{
  public function genUrl($route)
  {
    return str_replace(array('?', '&', '='), '/', $route);
  }
}