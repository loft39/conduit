<?php

namespace Conduit\Plugins;

use Conduit\Parsers\YamlParser;

class GenericPlugin
{

  private string $className;
  protected array $config;

  public function __construct() {
    $namespacedClass = explode("\\", get_class($this));
    $this->className = end($namespacedClass);

    $fullConfig = (new YamlParser())->configRead();

    $this->config = $fullConfig['plugins'][$this->className] ?? Array();
  }
}