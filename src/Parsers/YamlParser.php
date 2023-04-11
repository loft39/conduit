<?php

namespace Conduit\Parsers;

use Symfony\Component\Yaml\Yaml as Yaml;

class YamlParser {

  private Yaml $parser;

  function __construct() {
    $this->parser = new Yaml;
  }

  function configRead($path = null): array {
    if (!$path) {
      $path = __DIR__."/../../app/app.yml";
    }

    return $this->parser::parseFile($path);
  }
}