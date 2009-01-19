<?php
class sfUoWidgetPluginInstallationTask extends sfBaseTask
{
  /**
   * Configures the task
   * 
   * @access protected
   */
  protected function configure()
  {
    $this->namespace            = 'plugin';
    $this->name                 = 'uo-widget-install';
    $this->briefDescription     = '"sfUnobstrusiveWidgetPlugin" installation task';
    $this->detailedDescription  = <<<EOF
Install "sfUnobstrusiveWidgetPlugin" plugin's assets

Examples:
  [./symfony plugin:uo-widget-install]
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
    $this->checkSymlinks();
  }

  /**
   * Checks and creates missing symlinks
   * 
   * @access protected
   */
  protected function checkSymLinks()
  {
    $pluginsDir = sfConfig::get('sf_plugins_dir');

    $symlinks = array(
      'sf_unobstrusive_widget' => $pluginsDir.'/sfUnobstrusiveWidgetPlugin/web/sf_unobstrusive_widget',
    );

    $sourcePath = sfConfig::get('sf_web_dir');

    foreach ($symlinks as $source => $dest)
    {
      $file = $sourcePath.'/'.$source;

      if (file_exists($file))
      {
        unlink($file);
      }

      $this->logSection('link', 'Building link '.$source);

      if (!symlink($dest, $file))
      {
        throw new Exception('Could not create symlink "'.$dest.'"');
      }
    }
  }
}