<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoNestedSetArrayHelper
 * Nested set helper for sfUnobstrusiveWidgetPlugin.
 * Usefull since deal with array is faster than deal with objects.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoNestedSetArrayHelper
{
  protected
    $scopeKey         = 'scope',
    $treeLeftKey      = 'lft',
    $treeRightKey     = 'rgt',
    $valueKey         = 'id',
    $contentKey       = 'label',
    $attributesKey    = 'attributes',
    $_sortCallback    = null;

  /**
   * Set the callback for sorting elements. It will keep the root nodes order
   * (ordered by scope), but inner elements will be ordered according to callback
   *
   * @param string|array $callback A valid callback
   */
  public function setSortCallback($callback)
  {
    $this->_sortCallback = $callback;
  }

  public function setScopeKey($scopeKey)
  {
    $this->scopeKey = $scopeKey;
  }
  
  public function setTreeLeftKey($keyName)
  {
    $this->treeLeftKey = $keyName;
  }
  
  public function setTreeRightKey($keyName)
  {
    $this->treeRightKey = $keyName;
  }
  
  public function setValueKey($keyName)
  {
    $this->valueKey = $keyName;
  }
  
  public function setContentKey($keyName)
  {
    $this->contentKey = $keyName;
  }
  
  public function setAttributesKey($keyName)
  {
    $this->attributesKey = $keyName;
  }
  
  public function getScopeKey()
  {
    return $this->scopeKey;
  }
  
  public function getTreeLeftKey()
  {
    return $this->treeLeftKey;
  }
  
  public function getTreeRightKey()
  {
    return $this->treeRightKey;
  }
  
  public function getValueKey()
  {
    return $this->valueKey;
  }
  
  public function getContentKey()
  {
    return $this->contentKey;
  }
  
  public function getAttributesKey()
  {
    return $this->attributesKey;
  }

  /**
   * Parse an array of arrays to return a nested array.
   *
   * @param array $data
   * @return array
   */
  public function parse($data)
  {
    if (is_null($data))
    {
      return array();
    }
  
    $choices = array();
    foreach ($data as $element)
    {
      $scope = (int) $element[$this->scopeKey];

      if (!isset($choices[$scope]))
      {
        $choices[$scope] = array();
      }

      $choices[$scope][] = $element;
    }

    $results = array();
    foreach ($choices as $set)
    {
      $excludes = array();
      $results += $this->recursiveParse($set, $excludes);
    }

    return $results;
  }
  
  /**
   * Parse recursive.
   *
   * @param array $data            An array of arrays
   * @param array $excludes        An array of arrays value that allreay parsed
   * @param int   $parentTreeLeft  Parent tree left value
   * @param int   $parentTreeRight Parent tree right value
   *
   * @return boolean
   */
  protected function recursiveParse(array $data, &$excludes, $parentTreeLeft = null, $parentTreeRight = null)
  {
    $result          = array();
    
    foreach ($data as $dataRow)
    {
      if (count($excludes) == count($data))
      {
        break;
      }
    
      $id           = $dataRow[$this->valueKey];
      $label        = $dataRow[$this->contentKey];
      $treeLeft     = $dataRow[$this->treeLeftKey];
      $treeRight    = $dataRow[$this->treeRightKey];
      $attributes   = isset($dataRow[$this->attributesKey]) ? $dataRow[$this->attributesKey] : array();

      if (
        ((is_null($parentTreeLeft) && is_null($parentTreeRight)) 
        || ($treeLeft>$parentTreeLeft && $treeRight<$parentTreeRight))
        && !in_array($id, $excludes)
      )
      {
        $result[$id]['id']         = $id;
        $result[$id]['label']      = $label;
        $result[$id]['attributes'] = $attributes;
        
        $excludes[] = $id;
        $diff       = $treeRight - $treeLeft;
        if ($diff > 1)
        {
          $content  = $this->recursiveParse($data, $excludes, $treeLeft, $treeRight);
          if ($this->_sortCallback)
          {
            usort($content, $this->_sortCallback);
          }
          $result[$id]['contents'] = $content;
        }
      }
    }

    return $result;
  }

  public static function alphabeticalSort($a, $b)
  {
    $labelA = $a['label'];
    $labelB = $b['label'];

    if ($labelA < $labelB)
    {
      return -1;
    }
    else
    {
      return 1;
    }
  }
}