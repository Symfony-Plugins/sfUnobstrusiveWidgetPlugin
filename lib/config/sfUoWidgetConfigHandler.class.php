<?php

/**
 * Config handler for JS transformers and CSS skins.
 *
 * @package    sfUnobstrusiveWidgetPlugin
 * @subpackage lib.config
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetConfigHandler extends sfYamlConfigHandler
{
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

    return sprintf('<?php return %s;', var_export($config, 1));
  }

  protected function checkConfiguration($config)
  {
    // todo
  }
}
