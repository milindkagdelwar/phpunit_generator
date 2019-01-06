<?php

namespace Drupal\phpunit_generator;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\phpunit_generator\Renderer\FunctionalTestRenderer;

/**
 * @file handle the application to run through all process
 */

class Application {

  /**
   * @var ContainerInterface
   */
  protected $container;

  /**
   * @var Renderer
   */
  protected $renderer;

  /**
   * @var
   */
  protected $generator;

  /**
   * @var array
   */
  protected $params;

  /**
   * @var string
   */
  protected $testType;

  /**
   * Application constructor.
   */
  function __construct(array $params) {
    $this->registerServices();
    $this->params = $params;
    $this->testType = $params['test_type'];
    $this->setRenderer();
  }

  /**
   *
   */
  public function generateTestCase() {
    $renderer = $this->getRenderer();
    return $renderer->generate($this->params);
  }

  /**
   * Register all the service we will be using in application.
   */
  public function registerServices() {
    $this->setContainer();
  }

  /**
   * Set twig engine to render file anywhere
   */
  public function setEngine() {
    $engine = $this->container->get('generator.twig_engine');
    $this->renderer->setRendererEngine($engine);
  }

  public function getRenderer() {

    if(empty($this->renderer)) {
      $this->setRenderer();
    }
    return $this->renderer;
  }

  /**
   * Set renderer to render file anywhere
   */
  public function setRenderer() {

    //$test_type = ucfirst(strtolower($this->testType));
    //$class = filter_var($test_type . "TestRenderer", FILTER_SANITIZE_STRING);
    //$obj = (new ReflectionClass(CLASS_NAME))->newInstance();
    //$class = $test_type . 'TestRenderer';
    $this->renderer = new FunctionalTestRenderer();
    $this->renderer->setContainer($this->container);
    $this->setEngine();
  }

  /**
   * Set container for application.
   */
  public function setContainer() {
    $container = \Drupal::getContainer();
    $this->container = $container;
  }

}

