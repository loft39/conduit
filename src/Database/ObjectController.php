<?php

namespace Conduit\Database;

use Conduit\Objects\GenericObject;
use PDO;

class ObjectController extends Database {

  private string $regexPattern = "/^[a-zA-Z0-9]([a-zA-Z0-9_])+$/i";

  public function __construct(
    private readonly String $objectName,
    private Array|null $options = null
  )
  {
    parent::__construct();
    require($_SERVER['DOCUMENT_ROOT'] . "/../app/objects/" . $this->objectName . "Object.php");

    //Init default options
    if (!$options) {
      $this->options = [
          "includeUnpublished" => false
      ];
    }
  }

  public function readAll(): array|bool {

    $tableName = "obj_".$this->objectName;

    //Table name must only be Alpha+Underscore
    if (preg_match($this->regexPattern, $tableName)) {

      $query = "SELECT * FROM `$tableName`";

      if (!$this->options['includeUnpublished']) {
        $query .= " WHERE `published` = 1 ORDER BY `sortorder` ASC";
      }

      $obj = $this->dbObject->prepare($query);
      $obj->execute();

      // TODO: throw exception if class not found, maybe create a new exception,
      //  or potentially create a new FileNotFoundException for all areas in Conduit?
      return $obj->fetchAll(PDO::FETCH_CLASS, $this->objectName . 'Object');
    } else {
      //TODO: throw new invalid object name exception
      return false;
    }
  }

  public function read($field, $value): mixed {

    $tableName = "obj_".$this->objectName;

    //Table name and search field must only be Alpha+Underscore
    if (
      preg_match("/^[a-zA-Z0-9]([a-zA-Z0-9_])+$/i", $tableName) &&
      preg_match("/^[a-zA-Z0-9]([a-zA-Z0-9_])+$/i", $field)
    ) {
      $obj = $this->dbObject->prepare("SELECT * FROM `$tableName` WHERE `$field` = :value");
      $obj->execute([':value' => $value]);

      // TODO: throw exception if class not found, maybe create a new exception,
      //  or potentially create a new FileNotFoundException for all areas in Conduit?
      return $obj->fetchObject( $this->objectName . 'Object');
    } else {
      //TODO: throw new invalid object name exception
      return [
        "nah"
      ];
    }
  }
//
//  public function read($key): array {
//    $kv = $this->dbObject->prepare("SELECT * from `keyvalue` where `k` = :key");
//    $kv->execute([':key' => $key]);
//    return $kv->fetch();
//  }
//
//  public function write($key, $value): bool
//  {
//    $kv = $this->dbObject->prepare(
//      "INSERT INTO `keyvalue` (`k`, `v`)
//               VALUES (:key, :value)
//               ON DUPLICATE KEY UPDATE `v` = :value;");
//    $kv->execute([':key' => $key, ':value' => $value]);
//
//    return true;
//  }
//
//  public function delete($key): bool
//  {
//    $kv = $this->dbObject->prepare(
//        "DELETE FROM `keyvalue` where `k` = :key;");
//    $kv->execute([':key' => $key]);
//
//    return true;
//  }

}