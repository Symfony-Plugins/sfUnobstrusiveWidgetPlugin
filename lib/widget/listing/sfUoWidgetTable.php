<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetTable
 * Table widget rend a table.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetTable extends sfUoWidget
{
  public function getNbData()
  {
    return count($this->getOption('data'));
  }
  
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * choice_enable:                 Enable or not the choice in each row
   *  * choice_name:                   Choice input name
   *  * choice_type:                   Choice type ("checkbox" or "radio")
   *  * choice_position:               Checkbox position ("first" or "last")
   *  * row_number_enable:             Enable or not a number in each row
   *  * row_number_start:              Row number start
   *  * row_classname_enable:          Enable or not the class in each row
   *  * row_classname_odd:             Row odd classname
   *  * row_classname_even:            Row even classname
   *  * data:                          Data to inject in the table
   *  * no_data_message:               Message to display when data is empty
   *  * row_template_name:             Row template name
   *  * row_template_extra_vars:       Row template extra vars
   *  * header_template_name:          Header template name
   *  * header_template_extra_vars:    Header template extra vars
   *  * footer_template_name:          Footer template name
   *  * footer_template_extra_vars:    Footer template extra vars
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfUoWidget
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('data', array());
    $this->addOption('data_footer', array());

    $this->addOption('no_data_message', 'no data');
    $this->addOption('no_data_classname', 'no-data');

    $this->addOption('choice_enable', true);
    $this->addOption('choice_name', 'items');
    $this->addOption('choice_type', 'checkbox');
    $this->addOption('choice_position', 'last');
    
    $this->addOption('row_number_enable', true);
    $this->addOption('row_number_start', 1);

    $this->addOption('row_classname_enable', true);
    $this->addOption('row_classname_odd', 'odd');
    $this->addOption('row_classname_even', 'even');
    
    $this->addOption('row_template_name', null);
    $this->addOption('row_template_extra_vars', array());

    $this->addOption('header_template_name', null);
    $this->addOption('header_template_extra_vars', array());

    $this->addOption('footer_template_name', null);
    $this->addOption('footer_template_extra_vars', array());
  }
  
  /**
   * @return string An HTML tag string
   *
   * @see render()
   */
  protected function doRender()
  {
    $attributes['class'] = 'table_results';
    $headers             = $this->getOption('headers');
    $footer              = $this->getOption('footer');
    $data                = $this->getOption('data');

    if ((!is_array($data) || !$this->getNbData()) && !(is_array($headers) && count($headers)))
    {
      return $this->renderContentTag('p', $this->__($this->getOption('no_data_message')), array('class' => $this->getOption('no_data_classname')));
    }

    $result  = '';
    $result .= $this->getHeader();
    $result .= $this->getFooter();
    $result .= $this->getBody();


    if (!empty($result))
    {
      $result = $this->renderContentTag('table', $result, array_merge($this->getAttributes(), $attributes));
    }


    return $result;
  }
  
  /**
   * Return the header content
   *
   * @return string
   */
  protected function getHeader()
  {
  }
  
  /**
   * Return the footer content
   *
   * @return string
   */
  protected function getFooter()
  {
  }
  
  /**
   * Return the body content
   *
   * @return string
   */
  protected function getBody()
  {
    $result   = '';
    $data     = $this->getOption('data');
    $position = $this->getOption('row_number_start');

    if ($this->getNbData())
    {
      $odd = false;
      foreach ($data as $key => $value)
      {
        $result .= $this->getRow($data, $key, $value, $odd = !$odd, $position);
        $position ++;
      }
    }
    else
    {
      $headers = $this->getOption('headers');
      $result .= $this->renderContentTag(
        'tr', 
        $this->renderContentTag(
          'td', 
          $this->__($this->getOption('no_data_message')), array('class' => $this->getOption('no_data_classname'), 'colspan' => count($headers))
        )
      );
    }

    if (!empty($result))
    {
      $result = $this->renderContentTag('tbody', $result);
    }

    return $result;
  }

  protected function getRow($data, $key, $data, $odd, $position)
  {
    $vars             = $this->getOption('extra_partial_vars') ? $this->getOption('extra_partial_vars') : array();
    $vars['data']     = $data;
    $vars['odd']      = $odd;
    $vars['position'] = $position;
    $result           = $this->getTemplate($this->getOption('row_template_name'), $vars);

    if ($this->getNbData())
    {
      if ($this->getOption('row_number_enable'))
      {
        $result = $this->renderContentTag('td', $position) . $result;
      }

      if ($this->getOption('choice_enable'))
      {
        $choice = $this->renderContentTag(
          'td', 
          '<input type="'.$this->getOption('choice_type').'" class="'.$this->getOption('choice_type').'" name="'.$this->getOption('choice_name').'['.$this->getId($key, $data).']'.'" />'
        );

        switch ($this->getOption('choice_position'))
        {
          case 'first':
            $result = $choice.$result;
            break;

          case 'last':
            $result .= $choice;
            break;

          default:
            throw new Exception ('invalid value for "choice_position" option');
        }
      }
    }

    return $this->renderContentTag(
      'tr', 
      $result, 
      $this->getOption('row_classname_enable') ? array('class' => $odd ? $this->getOption('row_classname_odd') : $this->getOption('row_classname_even')) : array()
    );
  }

  /**
   * getTemplate - renders HTML output for object row
   *
   * @param  Doctrine_Record $object
   * @param  mixed $odd
   * @param  mixed $position
   * @return string
   */
  protected function getTemplate($template, array $vars)
  {
    if (empty($template))
    {
      throw new Exception('No available template sets');
    }

    if (false !== $sep = strpos($template, '/'))
    {
      $moduleName   = substr($template, 0, $sep);
      $templateName = substr($template, $sep + 1);
      $actionName   = '_'.$templateName;
    }
    else
    {
      throw new Exception('Template name must have a separator "/"');
    }

    $class = sfConfig::get('mod_'.$moduleName.'_partial_view_class', 'sf').'PartialView';
    $view  = new $class($this->getContext(), $moduleName, $actionName, '');
    $view->setPartialVars($vars);

    return $view->render();
  }
 
  /**
   * Gets the JavaScript selector.
   *
   * @return string A JS selector
   */
  protected function getJsSelector()
  {
    return 'uo_widget_table';
  }

  /**
   * Clone funtion.
   */
  public function __clone()
  {
    if ($this->getOption('data') instanceof sfCallable)
    {
      $callable = $this->getOption('data')->getCallable();
      if (is_array($callable))
      {
        $callable[0] = $this;
        $this->setOption('data', new sfCallable($callable));
      }
    }
  }
}
