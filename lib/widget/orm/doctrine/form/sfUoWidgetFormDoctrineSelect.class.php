<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormDoctrineSelect represents a select HTML tag for a model.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfWidgetFormDoctrineSelect.class.php 7746 2008-03-05 12:10:27Z fabien $
 */
class sfUoWidgetFormDoctrineSelect extends sfUoWidgetFormSelect
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
   * Constructor.
   *
   * Available options:
   *
   *  * model:      The model class (required)
   *  * add_empty:  Whether to add a first empty value or not (false by default)
   *                If the option is not a Boolean, the value will be used as the text value
   *  * method:     The method to use to display object values (__toString by default)
   *  * order_by:   An array composed of two fields:
   *                  * The column to order by the results (must be in the PhpName format)
   *                  * asc or desc
   *  * alias:      The alias for the main component involved in the query
   *  * query:      A query to use when retrieving objects
   *  * connection: The Doctrine connection to use (null by default)
   *
   * @see sfWidgetFormSelect
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('model');
    $this->addOption('add_empty', false);
    $this->addOption('method', '__toString');
    $this->addOption('method_value', 'getPrimaryKey');
    $this->addOption('method_attributes', null);
    $this->addOption('order_by', null);
    $this->addOption('alias', 'a');
    $this->addOption('query', null);
    $this->addOption('connection', null);

    parent::configure($options, $attributes);
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
      $choices[''] = (true === $this->getOption('add_empty')) ? '' : $this->getOption('add_empty');
    }

    $a = $this->getOption('alias');
    $q = is_null($this->getOption('query')) ? Doctrine::getTable($this->getOption('model'))->createQuery($a) : $this->getOption('query');

    if ($order = $this->getOption('order_by'))
    {
      $q->orderBy("$a." . $order[0] . ' ' . $order[1]);
    }

    $objects          = $q->execute();
    $method           = $this->getOption('method');
    $methodValue      = $this->getOption('method_value');
    $methodAttributes = $this->getOption('method_attributes');
    foreach ($objects as $object)
    {
      $data = array('label' => $object->$method());
      if ($methodAttributes)
      {
        $data['attributes'] = $object->$methodAttributes();
      }
      
      $choices[is_array($value = $object->$methodValue()) ? current($value) : $value] = $data;
    }

    return $choices;
  }

  public function __clone()
  {
    $this->setOption('choices', new sfCallable(array($this, 'getChoices')));
  }
}