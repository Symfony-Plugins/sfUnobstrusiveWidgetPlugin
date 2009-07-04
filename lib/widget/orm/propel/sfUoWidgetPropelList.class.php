<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetPropelList
 * Propel list widget rend a list from database using propel.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetPropelList extends sfUoWidgetList
{
  /**
   * @see sfUoWidget
   */
  public function __construct($options = array(), $attributes = array())
  {
    $options['choices'] = new sfCallable(array($this, 'getChoices'));

    parent::__construct($options, $attributes);
  }
  
  /**
   * Configures the current widget.
   *
   *  * model:          The model class (required)
   *  * add_empty:      Whether to add a first empty value or not (false by default)
   *                    If the option is not a Boolean, the value will be used as the text value
   *  * method:         The method to use to display object values ("__toString" by default)
   *  * peer_method:    The method to use to get objects ("doSelect" by default)
   *  * order_by:       An array composed of two fields:
   *                      * The column to order by the results (must be in the PhpName format)
   *                      * "asc" or "desc"
   *  * criteria:       A criteria to use when retrieving objects
   *  * connection:     The Propel connection to use (null by default)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfUoWidget
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
  
    $this->addRequiredOption('model');
    $this->addOption('method', '__toString');
    $this->addOption('attributes_method', null);
    $this->addOption('peer_method', 'doSelect');
    $this->addOption('order_by', null);
    $this->addOption('criteria', null);
    $this->addOption('connection', null);
  }
  
  /**
   * Return list choices.
   *
   * @return string
   */
  public function getChoices()
  {
    try
    {
      $this->checkObjectMethods();
    }
    catch (Exception $e)
    {
      throw $e;
    }
    
    $method           = $this->getOption('method');
    $atributesMethod  = $this->getOption('attributes_method');
    $choices          = array();
    $objects          = $this->getObjects();
    
    foreach ($objects as $object)
    {
      $choices[$object->getPrimaryKey()]['label'] = $object->$method();
      if (!empty($atributesMethod))
      {
        $choices[$object->getPrimaryKey()]['attributes'] = $object->$atributesMethod();
      }
    }

    return $choices;
  }
  
  /**
   * Return objects list.
   *
   * @return array
   */
  protected function getObjects()
  {
    $class = $this->getOption('model').'Peer';

    $criteria = is_null($this->getOption('criteria')) ? new Criteria() : clone $this->getOption('criteria');
    if ($order = $this->getOption('order_by'))
    {
      $method = sprintf('add%sOrderByColumn', 0 === strpos(strtoupper($order[1]), 'ASC') ? 'Ascending' : 'Descending');
      $criteria->$method(call_user_func(array($class, 'translateFieldName'), $order[0], BasePeer::TYPE_PHPNAME, BasePeer::TYPE_COLNAME));
    }

    return call_user_func(array($class, $this->getOption('peer_method')), $criteria, $this->getOption('connection'));
  }
  
  /**
   * Check object methods.
   * Throw an exception if a method missed.
   *
   * @return boolean
   */
  protected function checkObjectMethods()
  {
    $methods          = array('method');
    $atributesMethod  = $this->getOption('attributes_method');

    if (!empty($atributesMethod))
    {
      $methods[]  = 'attributes_method';
    }

    foreach ($methods as $method)
    {
      if (!method_exists($this->getOption('model'), $this->getOption($method)))
      {
        throw new RuntimeException(sprintf('Class "%s" must implement a "%s" method to be rendered in a "%s" widget', $this->getOption('model'), $this->getOption($method), __CLASS__));
      }
    }
    
    return true;
  }
}