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

/**
 * Test class for Assembla_API.
 */
class Assembla_APITest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Assembla_API
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Mock_Assembla_API;
    }

    /**
     * @covers Assembla_API::loadConfig
     * @todo Implement testLoadConfig().
     */
    public function testLoadConfig() {
      $test_file = new Zend_Config(array());

      // Test case of Zend_Config object being passed,
      // Core_APITest covers the file cases.
      $this->object->loadConfig($test_file);
      $this->assertEquals($this->object->getRawConfig(), $test_file);
    }
}