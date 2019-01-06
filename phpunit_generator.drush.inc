 <?php

use Drupal\phpunit_generator\Application;

/**
 * @file Drush command to generate phpunit test cases.
 */

function phpunit_generator_drush_command() {

  $commands['phpunit-generate'] = [
    'description' => 'Drush Command to generate PHPUnit test cases.',
    'aliases' => ['phpunitgen'],
    'arguments' => [
      'test_type' => 'Type of PHPUnit Test need to generate.',
      'content_type' => 'Content type to use for test.',
      'path' => 'Path to store test cases, or specify option as module.'
    ],
    'options' => [
      'module' => 'Name of the module.',
    ],
    'examples' => [
      'drush phpunitgen functional <content-type>' => 'Generate Functional test case for content type in same directory.',
      'drush phpunitgen functionaljs <content-type>' => 'Generate Functional Javascript test case for content type in same directory.',
      'drush phpunitgen kernel <content-type>' => 'Generate Kernel test case for content type in same directory.',
      'drush phpunitgen unit <content-type>' => 'Generate Unit test case for content type in same directory.',
      'drush phpunitgen functional <content-type> --module=<module-name>' => 'Generate Functional test case for content type in module directory.',
      'drush phpunitgen functional <content-type> <path-to-directory>' => 'Generate Functional test case for content type in specified directory.',
    ],
  ];

  return $commands;
}

/**
 * Call back function drush_phpunit_generator_drush_command_phpunit_generate()
 * The call back function name in the  following format
 *   drush_{module_name}_{item_id_for_command}()
 */
function drush_phpunit_generator_phpunit_generate($test_type = NULL, $content_type = NULL, $path = NULL) {
  $module = drush_get_option('module', NULL);

  $valid_types = ['functional', 'functionaljs', 'kernel', 'unit'];
  if(empty($test_type) || !in_array($test_type, $valid_types, TRUE)) {
   return drush_set_error('DRUSH_DRUPAL_ERROR_MESSAGE', dt("Please provide valid type. please check drush phpunitgen --help for more details."));
  }

  if (empty($content_type)) {
    return drush_set_error('DRUSH_DRUPAL_ERROR_MESSAGE', dt("Content type is required for this test. please check drush phpunitgen --help for more details."));
  }

  $params = [
    'test_type' => $test_type,
    'content_type' => $content_type,
    'path' => $path,
    'module' => $module
  ];
  $application = new Application($params);
  $result = $application->generateTestCase();

  if (isset($result['@error_code'])) {
    return drush_set_error('DRUSH_DRUPAL_ERROR_MESSAGE', dt('Error @error_code: @msg', $result));
  }

  if (!drush_confirm('Do you want to generate test case file?')) {
    return;
  }

  drush_print(dt('Status: @status, Message: @msg', $result));
  drush_complete_cache_clear();
}

