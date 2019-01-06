# PHPUnit Generator Drupal 8 module.
======================================================================================

Following drush command can be use to generate PHPUnit test cases.

# drush phpunitgen functional <content-type>
Generate Functional test case for content type in same directory.                                                                                                           
# drush phpunitgen functionaljs <content-type>
Generate Functional Javascript test case for content type in same directory.                                                                                                        
# drush phpunitgen kernel <content-type>
Generate Kernel test case for content type in same directory.
  
# drush phpunitgen unit <content-type>
Generate Unit test case for content type in same directory.    

# drush phpunitgen functional <content-type> --module=<module-name>
Generate Functional test case for content type in module directory.                                                                                   
# drush phpunitgen functional <content-type> <path-to-directory>
Generate Functional test case for content type in specified directory.    

# Arguments:
 test_type : Type of PHPUnit Test need to generate.                 
 content_type : Content type to use for test.                          
 path : Path to store test cases, or specify option as module.

# Options:
 --module : Name of the module.
 
 Note: Currently Application provide support for Functional Test Cases.
