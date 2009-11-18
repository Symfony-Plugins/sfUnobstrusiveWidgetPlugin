<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetAdminMenu
 * Admin menu widget rend a menu from a yaml definition.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetAdminMenu extends sfUoWidgetMenu
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
   * Available options:
   *
   *  * is_super_admin_method:  The isSuperAdmin method name ("isSuperAdmin" by default)
   *  * is_authenticated_method:  The isAuthenticated method name ("isAuthenticated" by default)
   *  * has_credential_method:  The hasCredential method name ("hasCredential" by default) 
   *  * has_permission_method:  The HasPermission method name ("hasPermission" by default)
   *
   * @see sfUoWidget->configure()
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    
    $this->addOption('is_super_admin_method', 'isSuperAdmin');
    $this->addOption('is_authenticated_method', 'isAuthenticated');
    $this->addOption('has_credential_method', 'hasCredential');
    $this->addOption('has_permission_method', 'hasPermission');
  }

  /**
   * @return string An HTML tag string
   *
   * @see render()
   */
  protected function doRender()
  {
    if ('' == $this->getRenderName())
    {
      throw new InvalidArgumentException('Empty name for sfAdminMenuWidget is not available.');
    }
    
    return parent::doRender();
  }

  /**
   * Return menu choices.
   *
   * @return array
   */
  public function getChoices()
  {
    $manager = sfUoWidgetHelper::getAdminMenuConfigManager();
    try
    {
      return $manager->getConfiguration($this->getRenderName());
    }
    catch (Exception $e)
    {
      throw $e;
    }
  }

  /**
   * Return list item content.
   *
   * @param  string
   * @param  mixed
   *
   * @return string
   */
  protected function getItemContent($key, $value)
  {
    if (
      $this->checkUserRights($value, 'credentials', $this->getOption('has_credential_method'))
      && $this->checkUserRights($value, 'permissions', $this->getOption('has_permission_method')))
    {
      return parent::getItemContent($key, $value);
    }
    else
    {
      return '';
    }
  }

  /**
   * hasCredential.
   *
   * @param  array $values       The values
   * @param  array $fieldName       The fieldName to test
   * @param  array $values       The user method to use
   *
   * @return boolean
   */
  protected function checkUserRights($values, $fieldName, $method)
  {
    if ($this->isSuperAdmin())
    {
      return true;
    }
  
    if (is_array($values) && array_key_exists($fieldName, $values) && !empty($values[$fieldName]))
    {
      if (!$this->isAuthenticated() || !method_exists($this->getUser(), $method))
      {
        return false;
      }

      $result = false;
      foreach ($values[$fieldName] as $key => $value)
      {
        $result = $this->getUser()->$method($value);
        if ($result)
        {
          break;
        }
      }
      return $result;
    }

    return true;
  }
  
  /**
   * isSuperAdmin.
   *
   * @return boolean
   */
  protected function isSuperAdmin()
  {
    if (!$this->isAuthenticated())
    {
      return false;
    }
  
    $isSuperAdminMethod = $this->getOption('is_super_admin_method');
    return method_exists($this->getUser(), $isSuperAdminMethod) ? $this->getUser()->$isSuperAdminMethod() : false;
  }
  
  /**
   * isAuthenticated.
   *
   * @return boolean
   */
  protected function isAuthenticated()
  {
    $isAuthenticatedMethod = $this->getOption('is_authenticated_method');
    return method_exists($this->getUser(), $isAuthenticatedMethod) ? $this->getUser()->$isAuthenticatedMethod() : false;
  }
}