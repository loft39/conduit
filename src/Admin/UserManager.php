<?php

namespace Conduit\Admin;

use Conduit\Database\Database;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class UserManager
{
  public static function init($event): void {

    $db = new Database();
    $twig = new Environment((new FilesystemLoader(__DIR__ . "/Templates")));

    echo "\n";

    $args = $event->getArguments();

    if (!isset($args[0])) {
      die("ERROR: Database type not supplied.\n");
    } else {
      $dbType = ucfirst($event->getArguments()[0]);

      switch (strtolower($dbType)) {
        case "mysql":
        case "mariadb":
          $dbContents = $twig->render("UserDB_MySQL.twig");
          break;
        case "postgresql":
          $dbContents = $twig->render("UserDB_Postgres.twig");
          break;
        case "sqlite":
          $dbContents = $twig->render("UserDB_SQLite.twig");
          break;
        default:
          die("ERROR: Supplied database type invalid or not supported.\nConduit supports the following DB types (case insensitive):\nMySQL, MariaDB, PostgreSQL, SQLite.");
          break;
      }

      echo "Initialising user table...\n";
      $db->dbObject->query($dbContents);
      echo "User table initialised!\n";
    }
  }
}