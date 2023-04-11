<?php

namespace Conduit\Database;

use PDO;
use PDOException;
use Conduit\Parsers\YamlParser;
use Conduit\Exceptions\Database\InvalidConfigException;
use Conduit\Exceptions\Database\DatabaseUnreachableException;
use Conduit\Exceptions\Database\InvalidDriverException;

class Database
{
  public PDO|null $dbObject;

  /**
   * @throws InvalidDriverException|DatabaseUnreachableException
   * @throws InvalidConfigException
   */
  public function __construct() {

    $config = (new YamlParser())->configRead();

    //If a DB config is defined, ensure it's formed correctly.
    if (array_key_exists("database", $config)){
      if(array_key_first($config['database']) == "mysql") {
        //throw exception if DB config malformed
        if (
            empty($config['database']['mysql']['host']) ||
            empty($config['database']['mysql']['user']) ||
            empty($config['database']['mysql']['pass']) ||
            empty($config['database']['mysql']['db'])
        ) {
          throw new InvalidConfigException("MySQL database config in config.yml is invalid.");
        }
      } elseif (array_key_first($config['database']) == "sqlite") {
        //throw exception if DB config malformed
        if (
            empty($config['database']['sqlite']['path'])
        ) {
          throw new InvalidConfigException("SQLite database config in config.yml is invalid.");
        }
      } else {
        throw new InvalidDriverException("Driver '".array_key_first($config['database'])."' is not supported.");
      }
    }

    $driver = (string)array_key_first($config['database']);

    if ($driver == "mysql") {

      $host = (string)$config['database'][$driver]['host'];
      $user = (string)$config['database'][$driver]['host'];
      $pass = (string)$config['database'][$driver]['host'];
      $db = (string)$config['database'][$driver]['host'];

      try {
        $this->dbObject = new PDO(
            "mysql:dbname=$db;host=$host;charset=utf8mb4",
            $user,
            $pass
        );

        return $this->dbObject;

      } catch (PDOException) {
        throw new DatabaseUnreachableException("Could not connect to MySQL database.");
      }

    } elseif ($driver == "sqlite") {

      $dbname = $config['sqlite']['name'] ?: "db.db";

      try {
        $sqlitePath = $_SERVER['DOCUMENT_ROOT']."../app/db/$dbname";
        $this->dbObject = new PDO("sqlite:$sqlitePath");

        return $this->dbObject;

      } catch (PDOException) {
        throw new DatabaseUnreachableException("Could not connect to SQLite database.");
      }
    } else {
      throw new InvalidDriverException("Driver '$driver' is not supported.");
    }

  }

}