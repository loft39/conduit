<?php

namespace Conduit\Plugins;

class PluginManager
{

  private array $listing;

  public function __construct()
  {
    $this->listing = array_diff(scandir($_SERVER['DOCUMENT_ROOT'].'/../plugins'), array('..', '.'));
  }

  public function loadPlugins(): void
  {
    foreach ($this->listing as $plugin) {
      if (is_dir($_SERVER['DOCUMENT_ROOT'] . "/../plugins/$plugin")) {
        require_once($_SERVER['DOCUMENT_ROOT'] . "/../plugins/$plugin/$plugin.php");
      }
    }
  }
}