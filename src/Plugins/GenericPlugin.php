<?php

namespace Conduit\Plugins;

use Conduit\Parsers\YamlParser;
use Conduit\Router\RouterController;

class GenericPlugin
{

  private string $className;
  protected array $config;
  protected RouterController $router;

  public function __construct(RouterController $rt) {
    $namespacedClass = explode("\\", get_class($this));
    $this->className = end($namespacedClass);

    $fullConfig = (new YamlParser())->configRead();

    $this->config = $fullConfig['plugins'][$this->className] ?? Array();

    $this->router = $rt;
  }

}
