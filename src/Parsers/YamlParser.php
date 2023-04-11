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
      if ($_SERVER['DOCUMENT_ROOT'] !== "") {
        $path = $_SERVER['DOCUMENT_ROOT'] . "/../app/app.yml";
      } else {
        $path = __DIR__ . "/../../../../../app/app.yml";
      }
    }

    return $this->parser::parseFile($path);
  }
}