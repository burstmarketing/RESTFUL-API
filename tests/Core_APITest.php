<?php

if (!defined("ASSEMBLA_REST_API_ROOT")) {
  define("ASSEMBLA_REST_API_ROOT", realpath(dirname(__DIR__)));
}

require_once ASSEMBLA_REST_API_ROOT . '/Autoload.php';
require_once ASSEMBLA_REST_API_ROOT . '/Core/API.php';


class Mock_Core_API extends Core_API {
  protected function _getRequest() {}
  protected function _getResponse() {}
}

/**
 * Test class for Core_API.
 */
class Core_APITest extends PHPUnit_Framework_TestCase {
    /**
     * @var Mock_Core_API
     */
    protected $object;

    protected function setUp() {
        $this->object = new Mock_Core_API;
    }

    public function testAddFilterThrowsExceptionForInvalidFormat() {
      $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testAddFilterThrowsExceptionForInvalidCallback() {
      $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testAddFilterAddsFilter() {
      $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testUseCacheIsInitiallyFalse() {
      $this->assertFalse($this->object->useCache());
    }

    public function testUseCacheReturnsCoreApiWhenSet() {
      $this->assertInstanceOf('Core_API', $this->object->useCache(true));
    }

    public function testUseCacheSetter() {
      $this->object->useCache(true);

      $this->assertTrue($this->object->useCache());
    }

    public function testConfigIsInitiallyNull() {
      $this->assertAttributeEquals(null, '_config', $this->object);
    }

    public function testConfigThrowsExceptionForNonJsonFiles() {
      // Try loading a non-json file into config
      $this->setExpectedException('Zend_Json_Exception');

      $this->object->loadConfig(ASSEMBLA_REST_API_ROOT . '/Autoload.php');
    }

    public function testConfigThrowsExceptionForUnreadableFile() {
      $this->setExpectedException('Assembla_Exception');

      $this->object->loadConfig('some-non-existent-file');
    }

    public function testLoadedConfigIsOfTypeZendConfigJson() {
      // Real config, _config should be an instance of Zend_Config_Json
      $this->object->loadConfig(ASSEMBLA_REST_API_ROOT . '/Assembla/etc/config.json');

      $this->assertAttributeInstanceOf('Zend_Config_Json', '_config', $this->object);
    }

    public function testGetConfigsReturnsArray() {
      $this->object->loadConfig(ASSEMBLA_REST_API_ROOT . '/Assembla/etc/config.json');

      $this->assertTrue(is_array($this->object->getConfigs()));
    }

    /**
     * @depends testGetConfigsReturnsArray
     **/
    public function testGetConfigsIsArrayOfZendConfigObjects() {
      $this->object->loadConfig(ASSEMBLA_REST_API_ROOT . '/Assembla/etc/config.json');

      foreach ($this->object->getConfigs() as $config) {
        $this->assertInstanceOf('Zend_Config', $config);
      }
    }

    public function testGetConfigExistingUri() {
      $this->object->loadConfig(ASSEMBLA_REST_API_ROOT . '/Assembla/etc/config.json');

      $this->assertEquals($this->object->getConfig('defaults/url'), 'https://api.assembla.com');
    }

    public function testGetConfigNonExistentUriReturnsFalse() {
      $this->object->loadConfig(ASSEMBLA_REST_API_ROOT . '/Assembla/etc/config.json');

      $this->assertFalse($this->object->getConfig('non-existent-key-with-no-slash'));
    }

    public function testGetConfigNonExistentUriWithSlashReturnsFalse() {
      $this->object->loadConfig(ASSEMBLA_REST_API_ROOT . '/Assembla/etc/config.json');

      // Throws fatal error
      //$this->assertEquals($this->object->getConfig('non-existent-key-with-a-/'), false);

      $this->markTestIncomplete('Test not yet implemented.');
    }

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

    public function testGetServiceReturnsFalseForNonExistentServices() {
      $this->object->loadConfig(ASSEMBLA_REST_API_ROOT . '/Assembla/etc/config.json');

      // Make sure false is returned for nonexistent services
      $this->assertFalse($this->object->getService('some-nonexistent-service'));
    }

    public function testGetServiceReturnsZendConfigObject() {
      $this->object->loadConfig(ASSEMBLA_REST_API_ROOT . '/Assembla/etc/config.json');

      // Make sure it returns a Zend_Config object for an existing service
      $this->assertInstanceOf('Zend_Config', $this->object->getService('ticket_by_id'));
    }

    public function testCallThrowsExceptionForInvalidMethodPrefixes() {
      $this->setExpectedException('Assembla_Exception');

      // anything other than post,put,delete,load are invalid prefixes
      $this->object->someExampleMethod();
    }

    public function testCallThrowsExceptionForNonExistentService() {
      $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testCallThrowsExceptionForServiceWithNoUrl() {
      $this->markTestIncomplete('This test has not been implemented yet.');
    }
}