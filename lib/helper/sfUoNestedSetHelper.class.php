<?php

/**
 * Nested set helper for sfUnobstrusiveWidgetPlugin.
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage lib.helper
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
    $arrayParser = new sfUoNestedSetArrayHelper();
    return $arrayParser->parse($this->getCollectionAsArray($objects));
  }

  /**
   * Transform an object collection in an array collection
   *
   * @return array
   */
  protected function getCollectionAsArray($objects)
  {
    $scopeMethod     = $this->getScopeMethod();
    $valueMethod     = $this->getValueMethod();
    $contentMethod   = $this->getContentMethod();
    $treeLeftMethod  = $this->getTreeLeftMethod();
    $treeRightMethod = $this->getTreeRightMethod();
    $atributesMethod = $this->getAttributesMethod();

    $results = array();
    foreach ($objects as $object)
    {
      $results[] = array(
        'id'         => $object->$valueMethod(),
        'label'      => $object->$contentMethod(),
        'scope'      => $object->$scopeMethod(),
        'lft'        => $object->$treeLeftMethod(),
        'rgt'        => $object->$treeRightMethod(),
        'attributes' => empty($atributesMethod) ? array() : $object->$atributesMethod(),
      );
    }

    return $results;
  }
}