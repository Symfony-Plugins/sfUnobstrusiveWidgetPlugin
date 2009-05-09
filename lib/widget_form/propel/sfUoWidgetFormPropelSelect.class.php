<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * mlzUnobstrusiveWidgetFormSelectMany represents a select HTML tag where you can select multiple values.
 *
 * @package    symfony
 * @subpackage mlzUnobstrusiveWidget
 * @author     François Béliveau
 */
class sfUoWidgetFormPropelSelect extends sfWidgetFormSelect
{
  /**
   * @see sfWidget
   */
  public function __construct($options = array(), $attributes = array())
  {
    $options['choices'] = new sfCallable(array($this, 'getChoices'));

    parent::__construct($options, $attributes);
  }

  /**
   * Configures the current widget.
   *
   *  * model:      The model class (required)
   *  * add_empty:  Whether to add a first empty value or not (false by default)
   *                If the option is not a Boolean, the value will be used as the text value
   *  * method:     The method to use to display object values (__toString by default)
   *  * peer_method:     The method to use to get objects (doSelect by default)
   *  * order_by:   An array composed of two fields:
   *                  * The column to order by the results (must be in the PhpName format)
   *                  * asc or desc
   *  * criteria:   A criteria to use when retrieving objects
   *  * connection: The Propel connection to use (null by default)
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
    $this->addOption('add_empty', false);
    $this->addOption('method', '__toString');
    $this->addOption('peer_method', 'doSelect');
    $this->addOption('order_by', null);
    $this->addOption('criteria', null);
    $this->addOption('connection', null);
  }

  /**
   * @return string An HTML tag string
   *
   * @see render()
   */
  protected function doRender()
  {
    if ($this->getOption('multiple'))
    {
      $attributes['multiple'] = 'multiple';

      if ('[]' != substr($name, -2))
      {
        $name .= '[]';
      }
    }

    $choices = $this->getOption('choices');
    if ($choices instanceof sfCallable)
    {
      $choices = $choices->call();
    }

    return $this->renderContentTag('select', "\n".implode("\n", $this->getOptionsForSelect($value, $choices))."\n", array_merge(array('name' => $name), $attributes));
  }

  /**
   * Returns the choices associated to the model.
   *
   * @return array An array of choices
   */
  public function getChoices()
  {
    $choices = array();
    if (false !== $this->getOption('add_empty'))
    {
      $choices[''] = true === $this->getOption('add_empty') ? '&nbsp;' : $this->getOption('add_empty');
    }

    $class = $this->getOption('model').'Peer';

    $criteria = is_null($this->getOption('criteria')) ? new Criteria() : clone $this->getOption('criteria');
    if ($order = $this->getOption('order_by'))
    {
      $method = sprintf('add%sOrderByColumn', 0 === strpos(strtoupper($order[1]), 'ASC') ? 'Ascending' : 'Descending');
      $criteria->$method(call_user_func(array($class, 'translateFieldName'), $order[0], BasePeer::TYPE_PHPNAME, BasePeer::TYPE_COLNAME));
    }
    $objects = call_user_func(array($class, $this->getOption('peer_method')), $criteria, $this->getOption('connection'));

    $method = $this->getOption('method');

    if (!method_exists($this->getOption('model'), $method))
    {
      throw new RuntimeException(sprintf('Class "%s" must implement a "%s" method to be rendered in a "%s" widget', $this->getOption('model'), $method, __CLASS__));
    }

    foreach ($objects as $object)
    {
      $choices[$object->getPrimaryKey()] = $object->$method();
    }

    return $choices;
  }
}