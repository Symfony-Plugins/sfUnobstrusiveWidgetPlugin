<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetPropelNestedList
 * Propel nested list widget rend a nested list from database using propel.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetPropelNestedList extends sfUoWidgetPropelList
{
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
    
    $objects      = $this->getObjects();
    $scopeMethod  = $this->getOption('scope_method');
    $choices      = array();
    foreach ($objects as $object)
    {
      $choices[$object->$scopeMethod()][] = $object;
    }
    
    $result = array();
    foreach ($choices as $values)
    {
      $excludes = array();
      $result  += $this->recursiveChoicesParser($values, $excludes);
    }
    return $result;
  }
    
  /**
   * Configures the current widget.
   *
   *  * tree_left_method:     The method to use to get tree left object values ("getTreeLeft" by default)
   *  * tree_right_method:    The method to use to get tree right object values ("getTreeRight" by default)
   *  * scope_method:         The method to use to get scope object values ("getScope" by default)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfUoWidget
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
  
    $this->addOption('tree_left_method', 'getTreeLeft');
    $this->addOption('tree_right_method', 'getTreeRight');
    $this->addOption('scope_method', 'getScope');
  }
  
  /**
   * Create recursive choices.
   *
   * @param array $objects     An array of objects
   * @param array $excludes    An array of objects PK that allreay parsed
   * @param integer $options   Parent tree left value
   * @param integer $options   Parent tree right value
   *
   * @return boolean
   */
  protected function recursiveChoicesParser(Array $objects, &$excludes, $parentTreeLeft = null, $parentTreeRight = null)
  {
    $result           = array();
    
    $method           = $this->getOption('method');
    $treeLeftMethod   = $this->getOption('tree_left_method');
    $treeRightMethod  = $this->getOption('tree_right_method');
    $atributesMethod  = $this->getOption('attributes_method');

    foreach ($objects as $object)
    {
      if (count($excludes) == count($objects))
      {
        break;
      }
    
      $id           = $object->getPrimaryKey();
      $label        = $object->$method();
      $treeLeft     = $object->$treeLeftMethod();
      $treeRight    = $object->$treeRightMethod();
      $attributes   = empty($atributesMethod) ? array() : $object->$atributesMethod();
      
      if (
        ((is_null($parentTreeLeft) && is_null($parentTreeRight)) 
        || ($treeLeft>$parentTreeLeft && $treeRight<$parentTreeRight))
        && !in_array($id, $excludes)
      )
      {
        $result[$id]['label']       = $label;
        $result[$id]['attributes']  = $attributes;
        
        $excludes[] = $id;
        $diff       = $treeRight - $treeLeft;
        if ($diff > 1)
        {
          $result[$id]['contents']  = $this->recursiveChoicesParser($objects, $excludes, $treeLeft, $treeRight);
        }
      }
    }

    return $result;
  }
  
  /**
   * Check object methods.
   * Throw an exception if a method missed.
   *
   * @return boolean
   */
  protected function checkObjectMethods()
  {
    try
    {
      parent::checkObjectMethods();
    }
    catch (Exception $e)
    {
      throw $e;
    }
    
    $methods = array('tree_left_method', 'tree_right_method', 'scope_method');
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