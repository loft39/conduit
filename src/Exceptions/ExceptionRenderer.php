<?php

namespace Conduit\Exceptions;

use Exception;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

class ExceptionRenderer
{

  private Environment $twig;

  public function __construct() {

    // Override default exception template if exception.twig is present in conduit template root
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/../app/templates/exception.twig")) {
      $this->twig   = new Environment((new FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . "/../app/templates")),[
          'debug' => true
      ]);
    } else {
      $this->twig   = new Environment((new FilesystemLoader(__DIR__ . "/../Templates")),[
          'debug' => true
      ]);
    }

    $this->twig->addExtension(new DebugExtension());
    $this->twig->addExtension(new IntlExtension());
  }

  public function render(Exception $e): void
  {
    try {
      echo $this->twig->render("exception.twig", ["exceptionType" => get_class($e), "exception" => $e]);
    } catch (Exception) {
      die("Could not render exception template");
    }
  }
}
