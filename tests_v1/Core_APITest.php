<?php

if (!defined("ASSEMBLA_REST_API_ROOT")) {
  define("ASSEMBLA_REST_API_ROOT", realpath(dirname(__DIR__)));
}

require_once ASSEMBLA_REST_API_ROOT . '/Autoload.php';
require_once ASSEMBLA_REST_API_ROOT . '/Core/API.php';


class Mock_Core_API extends Core_API {
  public function getRawConfig() {
    return $this->_config;
  }

  protected function _getRequest() {}
  protected function _getResponse() {}
}

/**
 * Test class for Core_API.
 */
class Core_APITest extends PHPUnit_Framework_TestCase {
    /**
     * @var Core_API
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new Mock_Core_API;
    }

    /**
     * @covers Core_API::useCache
     * @todo Implement testUseCache().
     */
    public function testUseCache() {
      // Initially false..
      $this->assertFalse($this->object->useCache());

      $this->assertInstanceOf('Core_API', $this->object->useCache(true));

      $this->assertTrue($this->object->useCache());
    }

    /**
     * @covers Core_API::loadConfig
     * @todo Implement testLoadConfig().
     */
    public function testLoadConfig() {
      // Ensure our config object isn't an object yet
      $this->assertEquals($this->object->getRawConfig(), null);

      // Try loading a non-json file into config
      try {
        $this->object->loadConfig(ASSEMBLA_REST_API_ROOT . '/Autoload.php');

        $this->fail('Expected Zend_Json_Exception');
      } catch (Zend_Json_Exception $e) {}

      // Should still be empty
      $this->assertEquals($this->object->getRawConfig(), null);

      // Try loading a non-readable file into config
      try {
        $this->object->loadConfig('some-non-existent-file');

        $this->fail('Expected Assembla_Exception for unreadable file.');
      } catch (Zend_Json_Exception $e) {
        $this->fail('Expected Assembla_Exception for unreadable file.');
      } catch (Assembla_Exception $e) {}

      // Real config, _config should be an instance of Zend_Config_Json
      $this->object->loadConfig(ASSEMBLA_REST_API_ROOT . '/Assembla/etc/config.json');
      $this->assertTrue($this->object->getRawConfig() instanceof Zend_Config_Json);
    }

    /**
     * @covers Core_API::getConfigs
     * @todo Implement testGetConfigs().
     */
    public function testGetConfigs() {
      $this->object->loadConfig(ASSEMBLA_REST_API_ROOT . '/Assembla/etc/config.json');

      // getConfigs needs to return an array of Zend_Config objects
      $getConfigs = $this->object->getConfigs();

      $this->assertTrue(is_array($getConfigs));

      foreach ($getConfigs as $config) {
        $this->assertInstanceOf('Zend_Config', $config);
      }
    }

    /**
     * @covers Core_API::getConfig
     * @todo Implement testGetConfig().
     */
    public function testGetConfig()
    {
      $this->object->loadConfig(ASSEMBLA_REST_API_ROOT . '/Assembla/etc/config.json');

      $this->assertEquals($this->object->getConfig('defaults/url'), 'https://api.assembla.com');

      $this->assertEquals($this->object->getConfig('non-existent-key-with-no-slash'), false);

      // Throws fatal..
      //$this->assertEquals($this->object->getConfig('non-existent-key-with-a-/'), false);
    }

    /**
     * @covers Core_API::setConfig
     * @todo Implement testSetConfig().
     */
    public function testSetConfig() {
      $this->object->loadConfig(ASSEMBLA_REST_API_ROOT . '/Assembla/etc/config.json');

      // Make sure nothings set there..
      $this->assertFalse($this->object->getConfig('non-existent-key'));

      $this->object->setConfig('non-existent-key', 'test-value');
      $this->object->setConfig('a-stdclass-obj',   new StdClass);

      // Now it should have 'test-value'
      $this->assertEquals($this->object->getConfig('non-existent-key'), 'test-value');
      $this->assertEquals($this->object->getConfig('a-stdclass-obj'), new StdClass);
    }

    /**
     * @covers Core_API::getService
     * @todo Implement testGetService().
     */
    public function testGetService() {
      $this->object->loadConfig(ASSEMBLA_REST_API_ROOT . '/Assembla/etc/config.json');

      // Make sure false is returned for nonexistent services
      $this->assertFalse($this->object->getService('some-nonexistent-service'));

      // Make sure it returns a Zend_Config object for an existing service
      $this->assertInstanceOf('Zend_Config', $this->object->getService('ticket_by_id'));
    }

    /**
     * @covers Core_API::__call
     * @todo Implement test__call().
     */
    public function test__call() {
      try {
        $this->object->someExampleMethod();

        $this->fail('Should throw Assembla_Exception for Invalid Method.');
      } catch (Assembla_Exception $e) {}
    }
}