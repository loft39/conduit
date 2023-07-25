<?php

namespace Conduit\Admin;

use Conduit\Database\Database;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class ObjectManager
{
  /**
   * @throws RuntimeError
   * @throws SyntaxError
   * @throws LoaderError
   */
  public static function create($event): void {

    $db = new Database();
    $twig = new Environment((new FilesystemLoader(__DIR__ . "/Templates")));

    echo "\n";

    $args = $event->getArguments();

    if (!isset($args[0])) {
      die("ERROR: New object name not supplied.\n");
    } else {
      $objName = ucfirst($event->getArguments()[0]);

      echo "Creating object '$objName'\n";
      echo "Creating table 'obj_$objName'...\n";
      $dbContents = $twig->render("ObjectDB.twig", ["name" => $objName]);
      $db->dbObject->query($dbContents);
      echo "Creating object class '{$objName}Object'...\n";
      $fileContents = $twig->render("ObjectClass.twig", ["name" => $objName]);
      if (!file_put_contents(__DIR__ . "/../../../../../app/objects/{$objName}Object.php", $fileContents)) {
        echo "Error creating object class.";
      }
      echo "Object $objName created!\n";
    }
  }
}