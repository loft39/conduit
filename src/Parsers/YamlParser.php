<?php

namespace Conduit\Parsers;

use Symfony\Component\Yaml\Yaml as Yaml;

class YamlParser
{

  private Yaml $parser;

  public function __construct()
  {
    $this->parser = new Yaml();
  }

  public function configRead($path = null): array
  {
    if (!$path) {
      if ($_SERVER['DOCUMENT_ROOT'] !== "") {
        $path = $_SERVER['DOCUMENT_ROOT'] . "/../app/app.yml";
      } else {
        $path = __DIR__ . "/../../../../../app/app.yml";
      }
    }

    $config = $this->parser::parseFile($path);

    /*
     * Visit every leaf node of the config array, and if a key begins with '$',
     * replace with its relevant environment variable.
     */
    array_walk_recursive($config, function (&$item) {
      if (str_starts_with($item ?? '', '$')) {
        $item = getenv(substr($item, 1));
      }
    });

    return $config;
  }
}
