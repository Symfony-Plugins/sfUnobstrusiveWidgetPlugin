<?php
class sfUoWidgetPluginUninstallTask extends sfBaseTask
{
  protected
    $pluginName = 'sfUnobstrusiveWidgetPlugin',
    $pluginPath,
    $pluginWebPath,
    $pluginDataPath;
  
  /**
   * Configures the task
   * 
   * @access protected
   */
  protected function configure()
  {
    $this->namespace            = 'uo-widget';
    $this->name                 = 'uninstall';
    $this->briefDescription     = '"sfUnobstrusiveWidgetPlugin" uninstall task';
    $this->detailedDescription  = <<<EOF
Uninstall "sfUnobstrusiveWidgetPlugin" plugin's assets

Examples:
  [./symfony uo-widget:uninstall]
EOF;
  }

  /**
   * Executes the task
   * 
   * @param array $arguments The CLI arguments array
   * @param array $options   The CLI options array
   * 
   * @access protected
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->pluginPath     = sfConfig::get('sf_plugins_dir').DIRECTORY_SEPARATOR.$this->pluginName;
    $this->pluginWebPath  = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.$this->pluginName;
    $this->pluginDataPath = $this->pluginPath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'assets';

    if (file_exists($this->pluginWebPath))
    {
      $this->logSection($this->pluginName, 'Remove published assets');
      $this->rmRecursive($this->pluginWebPath);
    }
  }

  /**
   * Remove a file or a dir and all contents
   * 
   * @param string $filepath   The filepath to remove
   *
   * @return boolean
   * 
   * @access protected
   */
  protected function rmRecursive($filepath)
  {
    if (is_dir($filepath) && !is_link($filepath))
    {
      if ($dh = opendir($filepath))
      {
        while (($filename = readdir($dh)) !== false)
        {
          if ($filename == '.' || $filename == '..')
          {
            continue;
          }

          if (!$this->rmRecursive($filepath.DIRECTORY_SEPARATOR.$filename))
          {
            throw new Exception($filepath.DIRECTORY_SEPARATOR.$filename.' could not be deleted');
          }
        }
        closedir($dh);
      }
      return rmdir($filepath);
    }
    
    return unlink($filepath);
  }
}