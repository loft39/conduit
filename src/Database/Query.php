<?php

namespace Conduit\Database;

use PDO;

class Query extends Database {

  public function __construct(
    private Array|null $options = null
  )
  {
    parent::__construct();
    require($_SERVER['DOCUMENT_ROOT'] . "/../app/objects/" . $this->objectName . "Object.php");

    //Init default options
    if (!$options) {
      $this->options = [
        "fetchMode" => FetchMode::Array
      ];
    }
  }

  public function execute($query): array|bool {
    $q = $this->dbObject->query($query);
    $q->execute();

    if ($this->options["fetchMode"] == FetchMode::Array) {
      return $q->fetchAll(PDO::FETCH_ASSOC);
    } else {
      return false;
    }
  }

}