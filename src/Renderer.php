<?php

/**
 * @file handle the rendering of template file using Twig Engine.
 */

namespace Drupal\phpunit_generator;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\phpunit_generator\Renderer\Engine\TwigEngine;
use Drupal\Component\Utility\Unicode;

abstract class Renderer {

  const MODULE_NOT_EXIST = 101;
  const FILE_NOT_WRITABLE = 102;
  const FILE_CREATED = 200;
  const UNKNOWN_ERROR = 103;

  /**
   * @var TwigEngine
   */
  protected $engine;

  /**
   * @var ContainerInterface
   */
  protected $container;

  /**
   * @var string
   */
  protected $module_handler;

  /**
   * @var string
   */
  protected $rootPath;

  /**
   * @param ContainerInterface $container
   */
  public function setContainer(ContainerInterface $container) {
    $this->container = $container;
    $this->setHandlers();
  }

  /**
   * @param $engine
   */
  public function setRendererEngine(TwigEngine $engine) {
    $this->engine = $engine;
  }

  /**
   * @param string $template
   * @param string $target
   * @param array  $parameters
   *
   * @return array
   */
  protected function handleFileIO(
    $template,
    $target,
    $parameters = []
  ) {
    /*if (!is_dir(dirname($target))) {
      if (!mkdir(dirname($target), 0777, true)) {
        throw new \InvalidArgumentException(
          sprintf(
            'Path "%s" is invalid. You need to provide a valid path.',
            dirname($target)
          )
        );
      }
    }*/

    $content_type = preg_replace("/[^A-Za-z0-9?!]/", '', $parameters['content_type']);
    $parameters['content_type_class'] = ucfirst($content_type);

    $content = $this->engine->render($template, $parameters);

    // At this point we have only directory but we need to create dynamic file according
    // to requirement.
    $target_file_name = $this->createTargetFileName($target, $parameters);
    if (!file_exists($target_file_name)) {
      if (file_put_contents($target_file_name, $content)) {
        return [
          '@status' => self::FILE_CREATED,
          '@msg' => "Test Case File {$target_file_name} created in {$target} Directory."
        ];
      }
    }

    return [
      '@status' => self::UNKNOWN_ERROR,
      '@msg' => "Unknown error occur, make sure Directory have write permission OR check file already exists in directory."
    ];
  }

  public function createTargetFileName($target, $parameters) {

    $target_file = $parameters['test_type_action'] . ".php";
    if ($parameters['content_type']) {
      $content_type = preg_replace("/[^A-Za-z0-9?!]/", '', $parameters['content_type']);
      $target_file = "{$target}/" .ucfirst($content_type) . $parameters['test_type_action'] . ".php";
    }

    return $target_file;
  }

  /**
   * @param string $module
   *   Module name for test generation.
   *
   * @param string $path
   *
   * @param string $test_type
   *
   * @return array
   *
   * If module name not specify then using path to place files.
   */
  public function getTargetPath($test_type, $module = NULL, $path = NULL) {

    if ($module) {
      if ($this->module_handler->moduleExists($module)) {
        $module_path = $this->module_handler->getModule($module)->getPath();
        $path = $this->getTestTypePath($test_type);
        $path = "{$this->rootPath}/{$module_path}/{$path}";
        if (file_exists($path)) {
          return ['path' => $path];
        }

        if (file_prepare_directory($path, FILE_CREATE_DIRECTORY)) {
          return ['path' => $path];
        }
        return $this->generateError(self::FILE_NOT_WRITABLE, 'File not writable exists');
      }
      return $this->generateError(self::MODULE_NOT_EXIST, 'Module not exists OR enable the module before running phpunitgen command.');
    }
    else {
      // Make sure we create directory in same folder if path not given.
      $current_dir = getcwd();

      $path = empty($path) ? "{$current_dir}" : rtrim($path,'/');
      $path = $path . "/" . $this->getTestTypePath($test_type);

      if($path && is_dir($path)) {
        return ['path' => $path];
      }

      if (file_prepare_directory($path, FILE_CREATE_DIRECTORY)) {
        return ['path' => $path];
      }
      return $this->generateError(self::FILE_NOT_WRITABLE, 'Make sure directory have writable permission.');
    }
  }

  /**
   * Helper function to get get path of test type file.
   *
   * @param string $test_type
   *
   * @return string
   *
   * @TODO Remove this function in future and make logic to get dynamic path.
   */
  public function getTestTypePath($test_type) {

    switch($test_type) {

      case 'functional':
        return "tests/src/Functional";
        break;
      case 'functionaljs':
        return "tests/src/FunctionalJavascript";
        break;
      case 'kernel':
        return "tests/src/Kernel";
        break;
      case 'unit':
        return "tests/src/Unit";
        break;
    }
  }

  /**
   * Function to set different handlers used to generate test.
   */
  public function setHandlers() {
    $this->setModuleHandler();
    $this->setRoot();
  }

  /**
   * Get module handler
   */
  public function setModuleHandler() {
    $this->module_handler = $this->container->get('module_handler');
  }

  /**
   * Set root path
   */
  public function setRoot() {
   $this->rootPath = $this->container->get('app.root');
  }

  /**
   * Throw error while phpunit generation.
   */
  public function generateError($error_code, $msg) {
    return ['@error_code' => $error_code, '@msg' => $msg];
  }

}
