<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetFormInputFile represents an upload HTML input tag.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau
 */
class sfUoWidgetFormInputFile extends sfUoWidgetFormInput
{
  /**
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormInput
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->setOption('type', 'file');
    $this->setOption('needs_multipart', true);
    
    $this->addOption('file_src', null);
    $this->addOption('is_image', false);
    $this->addOption('edit_mode', true);
    $this->addOption('with_delete', true);
    $this->addOption('delete_label', 'remove the current file');
    $this->addOption('template', '%file%<br />%input%<br />%delete% %delete_label%');
  }
  
  /**
   * @return string An HTML tag string
   *
   * @see render()
   */
  protected function doRender()
  {
    $result = parent::doRender();
    if ($this->getOption('file_src') && $this->getOption('edit_mode'))
    {
      $delete      = '';
      $deleteLabel = '';

      if ($this->getOption('with_delete'))
      {
        $deleteName = ']' == substr($name, -1) ? substr($name, 0, -1).'_delete]' : $name.'_delete';

        $delete = $this->renderTag('input', array_merge(array('type' => 'checkbox', 'name' => $deleteName), array()));
        $deleteLabel = $this->renderContentTag('label', $this->getOption('delete_label'), array_merge(array('for' => $this->generateId($deleteName))));
      }

      $result = strtr($this->getOption('template'), array('%input%' => $result, '%delete%' => $delete, '%delete_label%' => $deleteLabel, '%file%' => $this->getFileAsTag(array())));
    }

    return $result;
  }

  protected function getFileAsTag($attributes)
  {
    if ($this->getOption('file_src'))
    {
      if ($this->getOption('is_image'))
      {
        return $this->renderTag('img', array_merge(array('alt'=>' ', 'src' => $this->getOption('file_src'))), $attributes);
      }
      else
      {
        return $this->renderContentTag('a', $this->getOption('file_src'), array_merge(array('class'=>'blank', 'href' => $this->getOption('file_src'))), $attributes);
      }
    }
    
    return '';
  }
}