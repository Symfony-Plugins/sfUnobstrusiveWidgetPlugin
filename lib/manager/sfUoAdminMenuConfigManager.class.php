<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoAdminMenuConfigManager
 * Config manager for admin menu widget.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoAdminMenuConfigManager implements ArrayAccess
{
  protected
    $context       = null,
    $configuration = array();

  /**
   * Constructor
   */
  public function __construct($context)
  {
    $this->context = $context;
    $this->configuration = include($this->context->getConfiguration()->getConfigCache()->checkConfig('config/sfUoAdminMenu.yml'));
  }

  /**
   * Returns context
   *
   * @return sfContext
   */
  public function getContext()
  {
    return $this->context;
  }

  /**
   * Returns all configuration
   *
   * @return array
   */
  public function getAllConfiguration()
  {
    return $this->configuration;
  }

  /**
   * Returns global or menu specific configuration
   *
   * @throws InvalidArgumentException if specified behavior does not have configuration
   *
   * @param  string $menu If null, global configuration will be returned
   * @return void
   */
  public function getConfiguration($menu=null)
  {
    if (is_null($menu))
    {
      return $this->getAllConfiguration();
    }
    else
    {
      if (!isset($this->configuration[$menu]))
      {
        throw new InvalidArgumentException('Configuration for sfUoAdminMenu menu «'.$menu.'» is not available.');
      }
      return $this->configuration[$menu];
    }
  }

  /**
     * ArrayAccess: isset
     */
  public function offsetExists($offset)
  {
    return isset($this->configuration[$offset]);
  }

  /**
     * ArrayAccess: getter
     */
  public function offsetGet($offset)
  {
    return $this->configuration[$offset];
  }

  /**
     * ArrayAccess: setter
     */
  public function offsetSet($offset, $value)
  {
    throw new LogicException('Cannot use array access of admin menu manager in write mode.');
  }

  /**
     * ArrayAccess: unset
     */
  public function offsetUnset($offset)
  {
    throw new LogicException('Cannot use array access of admin menu manager in write mode.');
  }
}