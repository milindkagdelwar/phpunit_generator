<?php

/**
 * @file
 * Contains \Drupal\phpunit_generator\Renderer\FunctionalTestRenderer.
 */

namespace Drupal\phpunit_generator\Renderer;

use Drupal\phpunit_generator\Renderer;

/**
 * Class FunctionalTestRenderer
 *
 * @package Drupal\phpunit_generator\Renderer
 */
class FunctionalTestRenderer extends Renderer {

  /**
   * {@inheritdoc}
   */
  public function generate(array $parameters) {
    $module = $parameters['module'];
    $path = $parameters['path'];

    $target = $this->getTargetPath($parameters['test_type'], $module, $path);

    if (isset($target['@error_code'])) {
      return $target;
    }

    // Setting node create action
    // @TODO make it dynamic for other actions
    $parameters['test_type_action'] = 'nodeCreateTest';

    return $this->handleFileIO(
      'functional/nodeCreateTest.php.twig',
      $target['path'],
      $parameters
    );
  }

}
