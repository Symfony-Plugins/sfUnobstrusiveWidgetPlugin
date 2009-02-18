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
  protected
    $user     = null,
    $name     = '';

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
   * @see sfUoWidget->configure()
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    
    $this->addOption('user', null);
    $this->addOption('is_super_admin_method', 'isSuperAdmin');
  }

  /**
   * @return string An HTML tag string
   *
   * @see render()
   */
  protected function doRender()
  {
    $this->user = $this->getOption('user') ? $this->getOption('user') : sfContext::getInstance()->getUser();

    if ('' == $this->getRenderName())
    {
      throw new InvalidArgumentException('Empty name for sfAdminMenuWidget is not available.');
    }
    
    return parent::doRender();
  }

  /**
   * Return menu choices.
   *
   * @return string
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
    if ($this->hasCredential($value))
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
   *
   * @return string
   */
  protected function hasCredential($values)
  {
    if (is_array($values))
    {
      $authenticate = array_key_exists('authenticate', $values) && $values['authenticate'];
      $credential   = array_key_exists('credentials', $values) && !empty($values['credentials']);
      
      if (!$this->user->isAuthenticated() && ($authenticate || $credential))
      {
        return false;
      }

      $isSuperAdminMethod = $this->getOption('is_super_admin_method');
      $isSuperAdmin       = method_exists($this->user, $isSuperAdminMethod) ? $this->user->$isSuperAdminMethod() : false;

      if ($credential && !$isSuperAdmin)
      {
        $response = false;
        foreach ($values as $key => $value)
        {
          $response = $this->user->hasCredential($value);
          if ($response)
          {
            break;
          }
        }
        return $response;
      }
    }

    return true;
  }
}