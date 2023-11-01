<?php

namespace Conduit\Router;

use AltoRouter;
use Conduit\Middleware\GenericMiddleware;
use Twig\Environment;
use Exception;

use Conduit\Exceptions\Config\MalformedRoutesException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class RouterController
{
  private AltoRouter $altoRouter;
  private Environment $twig;
  private array $appConfig;
  private array $routes;
  private array $defaultData;

  /**
   * @throws MalformedRoutesException
   */
  public function __construct(AltoRouter $altoRouter, Environment $twig, array $appConfig, array $mountedPlugins)
  {
    $this->appConfig = $appConfig;
    $this->altoRouter = $altoRouter;
    $this->twig = $twig;
    $this->routes = [];

    $this->defaultData = [
        "conduit" => [
            "version" => "1.0.0"
        ],
        "app" => [
            "name" => $appConfig['name'],
            "version" => $appConfig['version'],
            "target" => $appConfig['target']
        ],
        "plugins" => $mountedPlugins
    ];

    if (!array_key_exists("routes", $this->appConfig)) {
      throw new MalformedRoutesException("'Routes' block not present in app.yml");
    } else {
      $this->routes = $this->appConfig['routes'];
    }
  }

  /**
   * @throws MalformedRoutesException
   * @throws Exception
   */
  public function attachRoutes(null|array $routes = null): void
  {
    $routesArray = $routes ?? $this->routes;

    foreach ($routesArray as $route=>$methods) {

      //Throw exceptions if the method (HTTP verb) or twig template path are missing.
      if (!array_key_exists('GET', $methods) && !array_key_exists('POST', $methods)) {
        throw new MalformedRoutesException("Invalid method(s) for route '$route' in app.yml");
      }

      foreach ($methods as $method=>$config) {
        $this->altoRouter->map(
          $method,
          $route,
          $config
        );
      }

    }
  }

  /**
   * @throws SyntaxError
   * @throws RuntimeError
   * @throws LoaderError
   * @throws MalformedRoutesException
   */
  public function matchRoutes(): void {

    $match = $this->altoRouter->match();

    // call the mapped pageController method or throw a 404
    if (is_array($match)) {

      $this->handleRoute($match);

    } else {
      header("Location: /404");
      exit;
    }
  }

  /**
   * @throws SyntaxError
   * @throws RuntimeError
   * @throws LoaderError
   * @throws MalformedRoutesException
   */
  public function handleRoute(array $match):void {

    $config = $match['target'];

    //Is there middleware specified?
    if (array_key_exists("middleware", $config)) {
      //If the file can't be found, throw a MalformedRoutesException
      if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/../app/middleware/".$config['middleware'].".php")) {
        throw new MalformedRoutesException("Middleware '".$config['middleware'].".php' not found in app/middleware/");
      } else {
        //require the middleware
        require($_SERVER['DOCUMENT_ROOT'] . "/../app/middleware/".$config['middleware'].".php");

        //get actual classname if the middleware is in a folder (has various '/' characters)
        $array = explode("/", $config['middleware']);

        /** @var GenericMiddleware $className */
        $className = end($array);

        //if there's a template listed...
        if (array_key_exists("template", $config)) {
          //Require the middleware and spread its return array after the default app data
          echo $this->twig->render(
              $config['template'],
              [...$this->defaultData, ...$className::register($match['params'])]
          );
          exit;
        } else {
          //otherwise just require the middleware.
          $exec = $className::register($match['params']);
          //and redirect if key is supplied
          if ($exec && array_key_exists("redirect", $config)) {
            header("Location: ".$config['redirect']);
          }
        }

      }
    } else {
      //If no middleware present, simply render the template with the default app data
      if (!array_key_exists('template', $config)) {
        throw new MalformedRoutesException("'Template' key missing from route in app.yml");
      } else {
        echo $this->twig->render($config['template'], $this->defaultData);
        exit;
      }
    }
  }
}
