<?php

namespace Conduit;

//Generic
use Exception;

//Packages
use AltoRouter;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

//Conduit
use Conduit\Parsers\YamlParser;
use Conduit\Router\RouterController;
use Conduit\Plugins\PluginManager;
use Conduit\Exceptions\ExceptionRenderer;

class App
{

  public AltoRouter $altoRouter;
  public Environment $twig;
  public YamlParser $yamlParser;
  public PluginManager $pluginManager;

  public RouterController $router;
  public ExceptionRenderer $exceptionRenderer;

  private array $appConfig;

  function __construct()
  {
    $this->exceptionRenderer = new ExceptionRenderer;

    try {
      $this->yamlParser = new YamlParser;

      //Pull config
      $this->appConfig = $this->yamlParser->configRead();
      $this->altoRouter = new AltoRouter;

      $this->pluginManager = new PluginManager;

      if ($this->appConfig['target'] == "development") {
        $this->twig = new Environment((new FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . "/../app/templates")),[
          'debug' => true
        ]);
        $this->twig->addExtension(new DebugExtension());
      } else {
        $this->twig = new Environment((new FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . "/../app/templates")));
      }
      $this->twig->addExtension(new IntlExtension());
      $this->router = new RouterController($this->altoRouter, $this->twig, $this->appConfig);
    } catch (Exception $e) {
      $this->exceptionRenderer->render($e);
    }
  }

  function run(): void
  {
    try {
      $this->pluginManager->loadPlugins();
      $this->router->attachRoutes();
      $this->router->matchRoutes();
    } catch (Exception $e) {
      $this->exceptionRenderer->render($e);
    }
  }

}