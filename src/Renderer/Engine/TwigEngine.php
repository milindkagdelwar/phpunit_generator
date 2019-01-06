<?php

/**
 * @file
 * Contains \Drupal\phpunit_generator\Renderer\Engine.
 */

namespace Drupal\phpunit_generator\Renderer\Engine;

/**
 * Class TwigEngine
 *
 * @package Drupal\phpunit_generator\Renderer\Engine
 */
class TwigEngine {

  /**
   * @var \Twig_Environment
   */
  protected $engine;

  /**
   * @var array
   */
  protected $skeletonDirs;

  /**
   * @param string $template
   * @param array  $parameters
   *
   */
  public function render($template, $parameters = []) {
    if (!$this->engine) {
      $this->engine = new \Twig_Environment(
        new \Twig_Loader_Filesystem($this->getSkeletonDirs()), [
          'debug' => true,
          'cache' => false,
          'strict_variables' => true,
          'autoescape' => false,
        ]
      );

    }

    return $this->engine->render($template, $parameters);
  }

  /**
   * @param array $skeletonDirs
   */
  public function setSkeletonDirs(array $skeletonDirs) {
    foreach ($skeletonDirs as $skeletonDir) {
      $this->addSkeletonDir($skeletonDir);
    }
  }

  /**
   * @param $skeletonDir
   */
  public function addSkeletonDir($skeletonDir) {
    if (is_dir($skeletonDir)) {
      $this->skeletonDirs[] = $skeletonDir;
    }
  }

  /**
   * @return array
   */
  public function getSkeletonDirs() {
    if (!$this->skeletonDirs) {
      $this->skeletonDirs[] = __DIR__ . '/../../../templates';
    }

    return $this->skeletonDirs;
  }


}
