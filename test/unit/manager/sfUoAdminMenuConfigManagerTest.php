<?php
$pluginPath = dirname(__FILE__).'/../../..';

include($pluginPath.'/test/bootstrap.php');
require_once($pluginPath.'/lib/manager/sfUoAdminMenuConfigManager.class.php');
require_once($pluginPath.'/lib/config/sfUoAdminMenuConfigHandler.class.php');


class sfUoAdminMenuConfigManagerMock extends sfUoAdminMenuConfigManager
{
  public function __construct($pluginPath)
  {
    $uoWidgetConfigHandler = new sfUoAdminMenuConfigHandler();
    $uoWidgetConfigHandler->initialize();
    
    $data = $uoWidgetConfigHandler->execute(array($pluginPath.'/test/data/sfUoAdminMenu.yml'));
    $data = str_replace(array('<?php return', '?>'), '', $data);
    $this->configuration = eval($data);
    
    echo '<pre>'.print_r($data, true).'</pre>'; exit;
  }
}



$t = new lime_test(0, new lime_output_color());


$configManager = new sfUoAdminMenuConfigManagerMock($pluginPath);