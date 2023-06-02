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

  public function save(GenericObject $object): bool {

    //TODO: accept an object, grab the DB fields from $object->getFields(), and persist it to the database.
    // create a new one if no ID, or if one exists with that ID, replace it.

    // Get the table name from the object by stripping "Object" from the end and
    // prepending "obj_".
    $tableName = "obj_" . substr(get_class($object), 0, -6);

    $existing = $this->dbObject->prepare("SELECT `id` from :table WHERE `id` = :id");

    $existing->execute([
      ":table" => $tableName,
      ":id" => $object->id()
    ]);

    $existing->fetch(PDO::FETCH_ASSOC);

    return true;


    /*
     *
     * GPT ANSWER
     *
     *
     */

      // Table name must only be Alpha+Underscore
      if (preg_match("/^[a-zA-Z0-9]([a-zA-Z0-9_])+$/i", $tableName)) {
        // Get the object properties
        $properties = get_object_vars($object);

        // Check if the object has an ID
        if (isset($object->id) && is_int($object->id)) {
          // Prepare the update query
          $query = "UPDATE `$tableName` SET ";
          $updateFields = [];
          foreach ($properties as $property => $value) {
            // Exclude properties that start with an underscore
            if (substr($property, 0, 1) !== '_' && $property !== 'id') {
              $updateFields[] = "`$property` = :$property";
            }
          }
          $query .= implode(', ', $updateFields);
          $query .= " WHERE `id` = :id;";
        } else {
          // Prepare the insert query
          $columns = [];
          $values = [];
          foreach ($properties as $property => $value) {
            // Exclude properties that start with an underscore
            if (substr($property, 0, 1) !== '_') {
              $columns[] = "`$property`";
              $values[] = ":$property";
            }
          }
          $query = "INSERT INTO `$tableName` (";
          $query .= implode(', ', $columns);
          $query .= ") VALUES (";
          $query .= implode(', ', $values);
          $query .= ");";
        }

        $statement = $this->dbObject->prepare($query);

        // Bind parameter values
        foreach ($properties as $property => $value) {
          if (substr($property, 0, 1) !== '_') {
            $statement->bindValue(":$property", $value);
          }
        }

        // Bind the ID if it exists
        if (isset($object->id) && is_int($object->id)) {
          $statement->bindValue(':id', $object->id);
        }

        // Execute the query
        if ($statement->execute()) {
          return true;
        } else {
          // TODO: handle save error if needed
          return false;
        }
      } else {
        // TODO: throw new invalid object name exception
        return false;
      }



  }

}