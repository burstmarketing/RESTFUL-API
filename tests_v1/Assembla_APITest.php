<?php

if (!defined("ASSEMBLA_REST_API_ROOT")) {
  define("ASSEMBLA_REST_API_ROOT", realpath(dirname(__DIR__)));
}

require_once ASSEMBLA_REST_API_ROOT . '/Autoload.php';
require_once ASSEMBLA_REST_API_ROOT . '/Assembla/API.php';

class Mock_Assembla_API extends Assembla_API {
  public function getRawConfig() {
    return $this->_config;
  }
}

class Assembla_APITest extends PHPUnit_Framework_TestCase {

    protected $object;

    protected function setUp() {
        $this->object = new Mock_Assembla_API;
    }

    public function testLoadConfigReturnsItselfIfAnInstanceOfZendConfig() {
      $test_config = new Zend_Config(array());

      $this->object->loadConfig($test_config);
      $this->assertEquals($this->object->getRawConfig(), $test_config);
    }

    // Covered by Core_API test
    public function testLoadConfigForFiles() {}
}