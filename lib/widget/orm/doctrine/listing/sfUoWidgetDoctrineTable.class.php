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
  /**
   * Configures the widget.
   *
   * @param arrya $options
   * @param array $attributes
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('primary_key_getter', 'getId');
  }

  /**
   * Returns the number of data in the widget.
   *
   * @return int
   */
  public function getNbData()
  {
    return count($this->getOption('data'));
  }

  /**
   * Returns the value referenced at the object's given key.
   *
   * @param string $key
   * @param Doctrine_Record $object
   * @return mixed
   */
  protected function getKeyValue($key, $object)
  {
    return call_user_func(array($object, $this->getOption('primary_key_getter')));
  }
}