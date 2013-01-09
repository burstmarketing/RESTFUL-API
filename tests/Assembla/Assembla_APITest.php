<?php

if (!defined("ASSEMBLA_REST_API_ROOT")) {
  define("ASSEMBLA_REST_API_ROOT", realpath(dirname(__DIR__)));
 }

require_once ASSEMBLA_REST_API_ROOT . '/../Autoload.php';
require_once ASSEMBLA_REST_API_ROOT . '/../Assembla/API.php';

class Assembla_APITest extends PHPUnit_Framework_TestCase {

  protected $object;

  protected function setUp() {
    $this->object = new Assembla_API;
  }

  public function testLoadConfigReturnsItselfIfAnInstanceOfZendConfig() {
    $test_config = new Zend_Config(array());

    $this->object->loadConfig($test_config);

    $this->assertAttributeEquals($test_config, '_config', $this->object);
  }

  // Covered by Core_API test
  public function testLoadConfigForFiles() {}
}