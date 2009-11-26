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
 * @author     Hugo Hamon <webmaster@apprendre-php.com>
 */
class sfUoWidgetTable extends sfUoWidget implements Countable
{
  /**
   * Countable interface implementation
   *
   * @see Countable
   * @see getNbData
   *
   * @return integer
   */
  public function count()
  {
    return $this->getNbData();
  }

  /**
   * Returns the number of data that feed the widget.
   *
   * @return integer
   */
  public function getNbData()
  {
    return count($this->getOption('data'));
  }

  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * data:                                    Data to inject in the table body
   *  * data_header:                             Data to inject in the table header
   *  * data_footer:                             Data to inject in the table footer
   *  *
   *  * no_data_message:                         Message to display when data is empty
   *  * no_data_classname:                       CSS classname added to no data message container
   *  *
   *  * choice_enable:                           Enable or not the choice in each row, false by default
   *  * choice_name:                             Choice input name, "items" by default
   *  * choice_type:                             Choice type ("checkbox" or "radio"), "checkbox" by default
   *  * choice_position:                         Checkbox position ("first" or "last"), "last" by default
   *  * choice_header:                           Content of the choice header, "&nbsp;" by default
   *  * choice_footer:                           Content of the choice footer, "&nbsp;" by default
   *  * choice_classname:                        CSS classname added to choice column, "choice" by default
   *  * choice_allow_empty:                      Whether or not to display the checkbox if its value is empty, "true" by default
   *  * choice_empty_value:                      Default value to use to replace the checkbox if its corresponding value is empty, "&nbsp;" by default
   *  *
   *  * row_number_enable:                       Enable or not a number in each row, false by default
   *  * row_number_start:                        Row number start value
   *  * row_number_header:                       Content of the row number header, "#" by default
   *  * row_number_footer:                       Content of the row number footer, "&nbsp;" by default
   *  * row_number_classname:                    CSS classname added to row number column, "number" by default
   *  *
   *  * row_classname_enable:                    Enable or not the class in each row, true by default
   *  * row_classname_odd:                       CSS classname added to each odd row, "odd" by default
   *  * row_classname_even:                      CSS classname added to each even row, "even" by default
   *  *
   *  * row_template_name:                       Row template path to use to render each row
   *  * row_template_extra_vars:                 Extra vars to give to the row template
   *  *
   *  * header_template_name:                    Row template path to use to render header
   *  * header_template_extra_vars:              Extra vars to give to the header template
   *  *
   *  * footer_template_name:                    Row template path to use to render footer
   *  * footer_template_extra_vars:              Extra vars to give to the footer template
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
    $this->addOption('data_header', array());
    $this->addOption('data_footer', array());

    $this->addOption('no_data_message', 'no data');
    $this->addOption('no_data_classname', 'no-data');

    $this->addOption('choice_enable', false);
    $this->addOption('choice_name', 'items');
    $this->addOption('choice_type', 'checkbox');
    $this->addOption('choice_position', 'last');
    $this->addOption('choice_header', '&nbsp;');
    $this->addOption('choice_footer', '&nbsp;');
    $this->addOption('choice_classname', 'choice');
    $this->addOption('choice_allow_empty', true);
    $this->addOption('choice_empty_value', '&nbsp;');

    $this->addOption('row_number_enable', false);
    $this->addOption('row_number_start', 1);
    $this->addOption('row_number_header', '#');
    $this->addOption('row_number_footer', '&nbsp;');
    $this->addOption('row_number_classname', 'number');

    $this->addOption('row_classname_enable', true);
    $this->addOption('row_classname_odd', 'odd');
    $this->addOption('row_classname_even', 'even');

    $this->addOption('row_template_name', null);
    $this->addOption('row_template_extra_vars', array());

    $this->addOption('header_template_name', null);
    $this->addOption('header_template_extra_vars', array());

    $this->addOption('footer_template_name', null);
    $this->addOption('footer_template_extra_vars', array());

    $this->setAttribute('class', 'table_results');
  }

  /**
   * @return string An HTML tag string
   *
   * @see render()
   */
  protected function doRender()
  {
    $dataHeader          = $this->getOption('data_header');
    $data                = $this->getOption('data');

    if (!$this->getNbData() && !(is_array($dataHeader) && count($dataHeader)))
    {
      return $this->renderContentTag('p', $this->__($this->getOption('no_data_message')), array('class' => $this->getOption('no_data_classname')));
    }

    $result  = '';
    $result .= $this->getHeader();
    $result .= $this->getFooter();
    $result .= $this->getBody();


    if (!empty($result))
    {
      $result = $this->renderContentTag('table', $result, $this->getAttributes());
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
    $dataHeader = $this->getOption('data_header');
    if (!count($dataHeader) && !$this->getOption('header_template_name'))
    {
      return '';
    }

    $vars         = $this->getOption('header_template_extra_vars');
    $vars['data'] = $dataHeader;

    $result = $this->getRow(
      $this->getOption('header_template_name'),
      $vars,
      false,
      false,
      $this->getOption('row_number_header'),
      $this->getOption('choice_header'),
      'th'
    );

    if (!empty($result))
    {
      $result = $this->renderContentTag('thead', $result);
    }

    return $result;
  }

  /**
   * Return the footer content
   *
   * @return string
   */
  protected function getFooter()
  {
    $dataFooter = $this->getOption('data_footer');
    if (!count($dataFooter) && !$this->getOption('footer_template_name'))
    {
      return '';
    }

    $vars         = $this->getOption('footer_template_extra_vars');
    $vars['data'] = $dataFooter;

    $result = $this->getRow(
      $this->getOption('footer_template_name'),
      $vars,
      false,
      false,
      $this->getOption('row_number_footer'),
      $this->getOption('choice_footer'),
      'th'
    );

    if (!empty($result))
    {
      $result = $this->renderContentTag('tfoot', $result);
    }

    return $result;
  }

  /**
   * Return the body content
   *
   * @return string
   */
  protected function getBody()
  {
    $result = '';
    $data   = $this->getOption('data');
    $number = $this->getOption('row_number_start');

    if ($this->getNbData())
    {
      $odd = false;
      $vars                = $this->getOption('row_template_extra_vars');
      $vars['data']        = $data;
      foreach ($data as $key => $value)
      {
        $vars['subject_key'] = $key;
        $vars['subject']     = $value;
        $vars['odd']         = $odd = !$odd;
        $vars['number']      = $number;
        $keyValue            = $this->getKeyValue($key, $value);

        $result .= $this->getRow(
          $this->getOption('row_template_name'),
          $vars,
          $this->getOption('row_classname_enable'),
          $odd,
          $number,
          $this->renderChoiceContentTag($keyValue),
          'td'
        );
        $number ++;
      }
    }
    else
    {
      $colspan  = $this->getOption('dataHeader') ? count($this->getOption('dataHeader')) : 1;
      $result  .= $this->renderContentTag(
        'tr',
        $this->renderContentTag(
          'td',
          $this->__($this->getOption('no_data_message')), array('class' => $this->getOption('no_data_classname'), 'colspan' => $colspan)
        )
      );
    }

    if (!empty($result))
    {
      $result = $this->renderContentTag('tbody', $result);
    }

    return $result;
  }

  /*
   * Return a row
   *
   * @param string $template
   * @param array $templateVars
   * @param boolean $oddable
   * @param boolean $odd
   * @param string $number
   * @param string $choice
   * @param string $type Cell type ("td" or "th")
   *
   * @return string
   */
  protected function getRow($template, array $templateVars, $oddable, $odd, $number, $choice, $type)
  {
    $result = $this->getTemplate($template, $templateVars);

    if ($this->getOption('row_number_enable'))
    {
      $result = $this->renderContentTag($type, $number, array('class' => $this->getOption('row_number_classname'))) . $result;
    }

    if ($this->getOption('choice_enable'))
    {
      $choiceContent = $this->renderContentTag($type, $choice, array('class' => $this->getOption('choice_classname')));
      switch ($this->getOption('choice_position'))
      {
        case 'first':
          $result = $choiceContent.$result;
          break;

        case 'last':
          $result .= $choiceContent;
          break;

        default:
          throw new Exception ('invalid value for "choice_position" option');
      }
    }

    return $this->renderContentTag(
      'tr',
      $result,
      $oddable ? array('class' => $odd ? $this->getOption('row_classname_odd') : $this->getOption('row_classname_even')) : array()
    );
  }

  /**
   * Returns the checkbox input for batch actions.
   *
   * @param string $keyValue
   * @return string
   */
  protected function renderChoiceContentTag($keyValue)
  {
    if (!$this->getOption('choice_allow_empty') && empty($keyValue))
    {
      return $this->getOption('choice_empty_value');
    }

    return '<input type="'.$this->getOption('choice_type').'" class="'.$this->getOption('choice_type').'" name="'.$this->getOption('choice_name').'['.$keyValue.']'.'" value="'.$keyValue.'" />';
  }

  /**
   * getTemplate - renders HTML output for object row
   *
   * @param  string $template The template path
   * @param  array $vars The template vars
   *
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
    foreach (array('data', 'data_header', 'data_footer') as $dataType)
    {
      if ($this->getOption($dataType) instanceof sfCallable)
      {
        $callable = $this->getOption($dataType)->getCallable();
        if (is_array($callable))
        {
          $callable[0] = $this;
          $this->setOption($dataType, new sfCallable($callable));
        }
      }
    }
  }

  protected function getKeyValue($key, $object)
  {
    return $key;
  }
}
