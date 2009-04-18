<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfUoWidgetConfigHandler
 * Config handler for JS transformers and CSS skins.
 *
 * @package    symfony
 * @subpackage sfUnobstrusiveWidgetPlugin
 * @author     François Béliveau  <francois.beliveau@my-labz.com>
 */
class sfUoWidgetConfigHandler extends sfYamlConfigHandler
{
  public function execute($configFiles)
  {
    // retrieve yaml data
    $config = $this->parseYamls($configFiles);

    return sprintf('<?php return %s;', var_export($config, 1));
  }
}