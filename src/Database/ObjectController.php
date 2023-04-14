<?php

namespace Conduit\Database;

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

  /*
   * TODO: functions;
   *
   * Options:
   * includeUnpublished | bool, default false |
   *
   * getAll()
   *
   */


  public function readAll(): array|bool {

    $tableName = "obj_".$this->objectName;

    //Table name must only be Alpha+Underscore
    if (preg_match($this->regexPattern, $tableName)) {

      $query = "SELECT * FROM `$tableName`";

      if (!$this->options['includeUnpublished']) {
        $query .= " WHERE `published` = 1";
      }

      $query .= " ORDER BY `sortorder` DESC";

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

  public function readAllWhere($field, $value): array|bool {

    $tableName = "obj_".$this->objectName;

    //Table name and search field must only be Alpha+Underscore
    if (
      preg_match("/^[a-zA-Z0-9]([a-zA-Z0-9_])+$/i", $tableName) &&
      preg_match("/^[a-zA-Z0-9]([a-zA-Z0-9_])+$/i", $field)
    ) {

      $query = "SELECT * FROM `$tableName` WHERE `$field` = :value";

      if (!$this->options['includeUnpublished']) {
        $query .= " AND `published` = 1";
      }

      $query .= " ORDER BY `sortorder` DESC";

      $obj = $this->dbObject->prepare($query);
      $obj->execute([':value' => $value]);

      // TODO: throw exception if class not found, maybe create a new exception,
      //  or potentially create a new FileNotFoundException for all areas in Conduit?
      return $obj->fetchAll(PDO::FETCH_CLASS, $this->objectName . 'Object');
    } else {
      //TODO: throw new invalid object name exception
      return false;
    }
  }

  public function readSingleWhere($field, $value): object|bool {

    $tableName = "obj_".$this->objectName;

    //Table name and search field must only be Alpha+Underscore
    if (
        preg_match("/^[a-zA-Z0-9]([a-zA-Z0-9_])+$/i", $tableName) &&
        preg_match("/^[a-zA-Z0-9]([a-zA-Z0-9_])+$/i", $field)
    ) {

      $query = "SELECT * FROM `$tableName` WHERE `$field` = :value";

      if (!$this->options['includeUnpublished']) {
        $query .= " AND `published` = 1";
      }

      $query .= " ORDER BY `sortorder` DESC";

      $obj = $this->dbObject->prepare($query);
      $obj->execute([':value' => $value]);

      // TODO: throw exception if class not found, maybe create a new exception,
      //  or potentially create a new FileNotFoundException for all areas in Conduit?
      return $obj->fetchObject($this->objectName . 'Object');
    } else {
      //TODO: throw new invalid object name exception
      return false;
    }
  }

  public function search($fields, $value): array|null {

    $tableName = "obj_".$this->objectName;
    $objects = null;

    //Value to search for must only be Alpha+Underscore
    if (
        preg_match("/^[a-zA-Z0-9]([a-zA-Z0-9_])+$/i", $value)
    ) {

      $query = "SELECT * FROM `$tableName` WHERE ";

      $fieldsSQL = Array();

      foreach ($fields as $field) {

        //Each search field must only be Alpha+Underscore
        if (preg_match("/^[a-zA-Z0-9]([a-zA-Z0-9_])+$/i", $field)) {

          $fieldsSQL[] = "`$field` LIKE '%$value%'";

        } else {
          //TODO: throw new invalid field name exception

        }
      }

      $query .= "(".implode(" OR ", $fieldsSQL).")";

      if (!$this->options['includeUnpublished']) {
        $query .= " AND `published` = 1";
      }

      $query .= " ORDER BY `sortorder` DESC";

      $obj = $this->dbObject->prepare($query);
      $obj->execute();

      // TODO: throw exception if class not found, maybe create a new exception,
      //  or potentially create a new FileNotFoundException for all areas in Conduit?
      $objects = $obj->fetchAll(PDO::FETCH_CLASS, $this->objectName . 'Object');

    } else {
      //TODO: throw new invalid value exception

    }

    return $objects;
  }

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