<?php
class sfUoWidgetPluginUnpublishTask extends sfBaseTask
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
    $this->pluginPath     = realpath(sfConfig::get('sf_plugins_dir').DIRECTORY_SEPARATOR.$this->pluginName);
    $this->pluginWebPath  = sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.$this->pluginName;
    $this->pluginDataPath = realpath($this->pluginPath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'assets');
  
    if ($this->isVersion('1.1'))
    {
      // hack for symfony 1.1
      sfSimpleAutoload::getInstance()->addDirectory($this->pluginPath);
    }
  
    $this->namespace            = 'uo-widget';
    $this->name                 = 'unpublish';
    $this->briefDescription     = '"sfUnobstrusiveWidgetPlugin" unpublish assets task';
    $this->detailedDescription  = <<<EOF
Unpublish "sfUnobstrusiveWidgetPlugin" assets

Examples:
  [./symfony uo-widget:unpublish]
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
  
  protected function isVersion($version)
  {
    return (defined('SYMFONY_VERSION') && substr(SYMFONY_VERSION, 0, strlen($version)) == $version);
  }
}
