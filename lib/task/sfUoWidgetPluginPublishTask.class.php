<?php
class sfUoWidgetPluginPublishTask extends sfUoWidgetPluginUnpublishTask
{
  /**
   * Configures the task
   *
   * @access protected
   */
  protected function configure()
  {
    parent::configure();
  
    $this->namespace            = 'uo-widget';
    $this->name                 = 'publish';
    $this->briefDescription     = '"sfUnobstrusiveWidgetPlugin" publish assets task';
    $this->detailedDescription  = <<<EOF
Publish "sfUnobstrusiveWidgetPlugin" assets

Examples:
  [./symfony uo-widget:publish]
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
    parent::execute($arguments, $options);

    $filesystem = new sfFilesystem();

    if (sfUoWidgetHelper::isDynamicsEnable())
    {
      $this->logSection($this->pluginName, 'Publish assets for dynamics');
      if (!$filesystem->mkdirs($this->pluginWebPath, 0777))
      {
        throw new Exception('Unable to create "'.$this->pluginName.'" dir');
      }

      $files = sfFinder::type('file')->prune('.svn')->discard('.svn')->name('*.jpg', '*.jpeg', '*.gif', '*.png', '*.swf')->in($this->pluginDataPath);

      foreach ($files as $file)
      {
        $target = str_replace($this->pluginDataPath, $this->pluginWebPath, $file);
        if (!is_dir(dirname($target)))
        {
          $filesystem->mkdirs(dirname($target), 0777);
        }
        $this->relativeSymlink($filesystem, $file, str_replace($this->pluginDataPath, $this->pluginWebPath, $file), true);
      }
    }
    else
    {
      $this->logSection($this->pluginName, 'Publish default assets');
      $this->relativeSymlink($filesystem, $this->pluginDataPath, $this->pluginWebPath, true);
    }
  }
  
  protected function relativeSymlink(sfFilesystem $filesystem, $originDir, $targetDir, $copyOnWindows = false)
  {
    if ($this->isVersion('1.1'))
    {
      return $filesystem->symlink($originDir, $targetDir, $copyOnWindows);
    }
    else
    {
      return $filesystem->relativeSymlink($originDir, $targetDir, $copyOnWindows);
    }
  }
}
