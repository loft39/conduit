<?php

namespace Conduit\Database;

use Conduit\Objects\GenericObject;
use PDO;
use PDOException;

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
        "includeUnpublished" => false,
        "sortByDateAdded"    => false,
        "limit"              => false
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

      if ($this->options['sortByDateAdded']) {
        $query .= " ORDER BY `dateadded` DESC";
      } else {
        $query .= " ORDER BY `sortorder` DESC";
      }

      if ($this->options['limit'] !== false) {
        $l = (int)$this->options['limit'];
        $query .= " LIMIT $l;";
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

      if ($this->options['sortByDateAdded']) {
        $query .= " ORDER BY `dateadded` DESC";
      } else {
        $query .= " ORDER BY `sortorder` DESC";
      }

      if ($this->options['limit'] !== false) {
        $l = (int)$this->options['limit'];
        $query .= " LIMIT $l;";
      }

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

      if ($this->options['sortByDateAdded']) {
        $query .= " ORDER BY `dateadded` DESC";
      } else {
        $query .= " ORDER BY `sortorder` DESC";
      }

      $query .= " LIMIT 1;";


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

      if ($this->options['sortByDateAdded']) {
        $query .= " ORDER BY `dateadded` DESC";
      } else {
        $query .= " ORDER BY `sortorder` DESC";
      }

      if ($this->options['limit'] !== false) {
        $l = (int)$this->options['limit'];
        $query .= " LIMIT $l;";
      }

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



  public function create(Array $fields): Bool | PDOException {

    if (empty($fields)) {
      // TODO: throw array empty exception
    }

    $tableName = "obj_".$this->objectName;

    // Get generic fields from a new, blank GenericObject
    $genericFields = [];
    $genericFieldArray = (new GenericObject())->getFields();
    foreach ($genericFieldArray as $k=>$v) {
      $genericFields[] = $k;
    }

    // Sets the default values for $genericFields
    $defaultFields = [
      "sortorder" => 0,
      "dateadded" => "NOW()",
      "published" => 1
    ];

    // Inits the arrays used to build the SQL query
    $columns = [];
    $values = [];
    $execute = [];

    // Add fields from the create() method's array argument
    foreach ($fields as $property => $value) {

      // if it's one of the default values, overwrite the value in $defaultFields,
      if (in_array($property, $genericFields)) {
        $defaultFields[$property] = $value;
      } else {
        // otherwise add the column and value to their respective arrays
        $columns[] = "`$property`";
        $values[] = ":$property";
        $execute[":$property"] = $value;
      }
    }

    $query = "INSERT INTO `$tableName` (";
    $query .= "`id`, `sortorder`, `dateadded`, `published`, ";
    $query .= implode(', ', $columns);
    $query .= ") VALUES (";
    $query .= "NULL, ".$defaultFields['sortorder'].", ".$defaultFields['dateadded'].", ".$defaultFields['published'].", ";
    $query .= implode(', ', $values);
    $query .= ");";

    try {
      $q = $this->dbObject->prepare($query);
      $q->execute($execute);
      return true;
    } catch (PDOException $e) {
      return $e;
    }
  }

  public function save(GenericObject $object): bool {

    //TODO: accept an object, grab the DB fields from $object->getFields(), and persist it to the database.
    // create a new one if no ID, or if one exists with that ID, replace it.

    // Get the table name from the object by stripping "Object" from the end and
    // prepending "obj_".
    $tableName = "obj_" . substr(get_class($object), 0, -6);

    $fields = $object->getFields();










    // if the object has an ID already, it's an UPDATE query.
    if (is_int($object->id())) {

      $existing = $this->dbObject->prepare("SELECT `id` from :table WHERE `id` = :id");

      $existing->execute([
          ":table" => $tableName,
          ":id" => $object->id()
      ]);

      if ($existing->fetch(PDO::FETCH_ASSOC)) {
        // It matches an existing object, update it.
        // UPDATE
      } else {
        // TODO: throw error, object has ID but it doesn't match an existing entry in the table.
        //  new objects to be inserted should have NULL ID.
      }

    } else {
      // the object doesn't have an ID, it's a new addition.
      // INSERT
    }

    return true;




  }

}