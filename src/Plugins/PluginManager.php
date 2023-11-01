<?php

namespace Conduit\Plugins;

use Conduit\Router\RouterController;

class PluginManager
{

  private array $listing;
  private array $mountedPlugins;
  private RouterController $routerPassthrough;

  public function __construct(RouterController $router)
  {
    $this->listing = array_diff(scandir($_SERVER['DOCUMENT_ROOT'].'/../plugins'), array('..', '.'));
    $this->routerPassthrough = $router;
  }

  public function loadPlugins(): void
  {
    foreach ($this->listing as $plugin) {
      if (is_dir($_SERVER['DOCUMENT_ROOT'] . "/../plugins/$plugin")) {
        require_once($_SERVER['DOCUMENT_ROOT'] . "/../plugins/$plugin/$plugin.php");
        $fullClassName = "\Conduit\Plugins\\".$plugin";
        $this->mountedPlugins[$plugin] = (new $fullClassName($this->routerPassthrough));
      }
    }
  }

  public function mountedPlugins(): array
  {
    return $this->mountedPlugins;
  }
}
