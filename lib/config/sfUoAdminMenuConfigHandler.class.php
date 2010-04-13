<?php

/**
 * Config handler for admin menu widget.
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage lib.config
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoAdminMenuConfigHandler extends sfYamlConfigHandler
{
  protected static
    $allowedKey = array('label', 'route', 'absolute', 'url', 'credentials', 'permissions', 'contents');

  public function execute($configFiles)
  {
    // retrieve yaml data
    $config = $this->parseYamls($configFiles);

    try
    {
      $this->checkConfiguration($config);
    }
    catch(Exception $e)
    {
      throw $e;
    }

    $code   = sprintf('<?php return %s;', var_export($config, 1));

    return $code;
  }

  protected function checkConfiguration($config)
  {
    if (empty($config))
    {
      return true;
    }
    
    try
    {
      return $this->recursiveCheckConfiguration($config);
    }
    catch (Exception $e)
    {
      throw $e;
    }
  }
  
  protected function recursiveCheckConfiguration($config)
  {
    $result = true;
    foreach ($config as $key => $value)
    {
      if (!in_array($key, array('credentials', 'permissions')) && is_array($value))
      {
        try
        {
          $result = $this->recursiveCheckConfiguration($value);
        }
        catch (Exception $e)
        {
          throw new InvalidArgumentException($key.'->'.$e->getMessage());
        }
      }
      else if (!in_array($key, self::$allowedKey))
      {
        throw new InvalidArgumentException($key.' : unavailable key.');
      }
    }
    
    return $result;
  }
}
