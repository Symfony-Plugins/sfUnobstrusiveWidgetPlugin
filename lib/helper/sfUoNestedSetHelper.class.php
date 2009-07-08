<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoNestedSetHelper
 * Nested set helper for sfUnobstrusiveWidgetPlugin.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoNestedSetHelper
{
  protected
    $scopeMethod      = 'getScope',
    $treeLeftMethod   = 'getTreeLeft',
    $treeRightMethod  = 'getTreeRight',
    $valueMethod      = 'getPrimaryKey',
    $contentMethod    = '__toString',
    $attributesMethod = '';

  public function setScopeMethod($methodName)
  {
    $this->scopeMethod = $methodName;
  }
  
  public function setTreeLeftMethod($methodName)
  {
    $this->treeLeftMethod = $methodName;
  }
  
  public function setTreeRightMethod($methodName)
  {
    $this->treeRightMethod = $methodName;
  }
  
  public function setValueMethod($methodName)
  {
    $this->valueMethod = $methodName;
  }
  
  public function setContentMethod($methodName)
  {
    $this->contentMethod = $methodName;
  }
  
  public function setAttributesMethod($methodName)
  {
    $this->attributesMethod = $methodName;
  }
  
  public function getScopeMethod()
  {
    return $this->scopeMethod;
  }
  
  public function getTreeLeftMethod()
  {
    return $this->treeLeftMethod;
  }
  
  public function getTreeRightMethod()
  {
    return $this->treeRightMethod;
  }
  
  public function getValueMethod()
  {
    return $this->valueMethod;
  }
  
  public function getContentMethod()
  {
    return $this->contentMethod;
  }
  
  public function getAttributesMethod()
  {
    return $this->attributesMethod;
  }

  /**
   * Parse an array of objects or a Doctrine_Colection to return a nested array.
   *
   * @param mixed $objects
   *
   * @return array
   */
  public function parse($objects)
  {
    $scopeMethod  = $this->getScopeMethod();
    $choices      = array();
    foreach ($objects as $object)
    {
      $choices[$object->$scopeMethod()][] = $object;
    }
    
    $results = array();
    foreach ($choices as $values)
    {
      $excludes = array();
      $results += $this->recursiveParse($values, $excludes);
    }
    return $results;
  }
  
  /**
   * Parse recursive.
   *
   * @param array $objects     An array of objects
   * @param array $excludes    An array of objects value that allreay parsed
   * @param integer $options   Parent tree left value
   * @param integer $options   Parent tree right value
   *
   * @return boolean
   */
  protected function recursiveParse(array $objects, &$excludes, $parentTreeLeft = null, $parentTreeRight = null)
  {
    $result          = array();
    
    $valueMethod     = $this->getValueMethod();
    $contentMethod   = $this->getContentMethod();
    $treeLeftMethod  = $this->getTreeLeftMethod();
    $treeRightMethod = $this->getTreeRightMethod();
    $atributesMethod = $this->getAttributesMethod();

    foreach ($objects as $object)
    {
      if (count($excludes) == count($objects))
      {
        break;
      }
    
      $id           = $object->$valueMethod();
      $label        = $object->$contentMethod();
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
          $result[$id]['contents']  = $this->recursiveParse($objects, $excludes, $treeLeft, $treeRight);
        }
      }
    }

    return $result;
  }
}