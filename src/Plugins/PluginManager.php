<?php

namespace Conduit\Plugins;

class PluginManager
{

  private array $listing;

  public function __construct()
  {
    $this->listing = array_diff(scandir(__DIR__.'/../../plugins'), array('..', '.'));
  }

  public function loadPlugins(): void
  {
    foreach ($this->listing as $plugin) {
      if (is_dir(__DIR__."/../../plugins/$plugin")) {
        require_once(__DIR__ . "/../../plugins/$plugin/$plugin.php");
      }
    }
  }
}