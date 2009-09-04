<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetDoctrineTable
 * Table widget rend a table.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetDoctrineTable extends sfUoWidgetTable
{
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('primary_key_getter', 'getId');
  }
  
  public function getNbData()
  {
    return $this->getOption('data')->count();
  }
  
  protected function getKeyValue($key, $object)
  {
    return call_user_func(array($object, $this->getOption('primary_key_getter')));
  }
}
