<?php

require('../Autoload.php');
require('constants.php');

define('DATA_PROVIDERS', realpath('./data_providers'));
  
class Assembla_APITest extends PHPUnit_Framework_TestCase {
  
  protected $_obj;

  protected $_api_obj;

  public function setUp() {
    $this->_obj = new Assembla_API;

    // Data Providers 
    $fh = fopen(RESTFUL_API_LOADER::getBaseDir() . 'load_config_test.json', 'w');
    fwrite($fh, '{ "foo": { "bar": "baz" } }');
  }

  public function tearDown() {
    unlink(RESTFUL_API_LOADER::getBaseDir() . 'load_config_test.json');
  }

  protected static function getMethod($name) {
    $class = new ReflectionClass('Assembla_API');
    $method = $class->getMethod($name);
    $method->setAccessible(true);

    return $method;
  }

  protected function _uc_words($str, $destSep='_', $srcSep='_') {
    return str_replace(' ', $destSep, ucwords(str_replace($srcSep, ' ', $str)));
  }
  
  protected function _camelize($name) {
    return $this->_uc_words($name, '');
  }

  /**
   * @test
   */
  public function callServices() {
    $this->_api_obj = new Assembla_API;
    $this->_api_obj->loadConfig('./Assembla/etc/config.json');

    $api = &$this->_api_obj;

    $api->setUserName(ASSEMBLA_API_USERNAME);
    $api->setPassword(ASSEMBLA_API_PASSWORD);
    
    foreach ($api->getConfig('services') as $service_slug => $service) {
      $testMethodName = $this->_camelize($service_slug);
      
      if (method_exists($this, $testMethodName)) {
	$this->assertInstanceOf($api->getConfig('services/' . $service_slug . '/classname'), $this->$testMethodName());
      }
    }
  }

  // url overwrites uri

  public function _ShowSpaceById($id) {
    return $this->_api_obj->loadShowSpaceById($id);
  }

  /**
   * @depends _ShowSpaceById
   *
   * @covers Service::my_spaces_list
   * @covers Service::show_space_by_id
   */
  public function MySpacesList() {
    $api = &$this->_api_obj;

    $api_request = new Assembla_API_Request;

    $service = $api->getService('my_spaces_list');

    // Assure that the service is a Zend_Config obj
    $this->assertInstanceOf('Zend_Config', $service);

    $api_request->setKey('my_spaces_list');

    $this->assertNotEquals(false, $service->getConfig('uri'));
    $this->assertNotEquals(false, $service->getConfig('type'));

    $api_request->setType($service->getConfig('type'));
    $api_request->setUri($service->getConfig('uri'));

    if (isset($service->url)) {
      $api_request->setUrl($service->url);
    } else if ($api->getConfig('defaults/url')) {
      $api_request->setUrl($api->getConfig('defaults/url'));
    }

    $api_request->setUsername($api->getConfig('credentials/username'));
    $api_request->setPassword($api->getConfig('credentials/password'));

    $return_spaces_list = $api_request->send();
    $return_spaces_list = preg_replace('/\<spaces\-tools(.*)\>(.*)\<\/spaces\-tools\>/s', '', $return_spaces_list);

    $actualXmlStructure = new DOMDocument();
    $actualXmlStructure->loadXML($return_spaces_list);

    $sampleXmlStructure = new DOMDocument();
    $sampleXmlStructure->loadXML(
   '<spaces type="array">
    <space>
    <name>Assembla API Test Bed</name>
    <default-showpage>Source/SVN</default-showpage>
    <created-at type="datetime">2012-03-26T14:49:23Z</created-at>
    <is-commercial type="boolean">true</is-commercial>
    <watcher-permissions type="integer">1</watcher-permissions>
    <updated-at type="datetime">2012-03-26T14:49:23Z</updated-at>
    <wiki-name>assembla-api-test-bed</wiki-name>
    <team-permissions type="integer">2</team-permissions>
    <is-manager type="boolean">true</is-manager>
    <can-join type="boolean">false</can-join>
    <id>dGGiO6D1ir4B_BacwqjQWU</id>
    <parent-id>dl9rxMqrar4iUWeJe5cbCb</parent-id>
    <public-permissions type="integer">0</public-permissions>
    <description>A test bed space for developing  the PHP implementation of the Assembla API - designed to allow for creation of tickets,  spaces,  adding and removing users and other mutative operations </description>
    <is-volunteer type="boolean">false</is-volunteer>    
    </space>
    </spaces>');    

    $this->assertEqualXmlStructure($sampleXmlStructure->firstChild, $actualXmlStructure->firstChild, true);    
 
    return $api->loadMySpacesList();
  }

  protected function _send() {
      
  }

  /**
   * @test
   */
  public function loadConfigFileNotExists() {
    $this->setExpectedException('Exception');

    $this->_obj->loadConfig('non-existent-file.json');
  }

  /**
   * @test
   */
  public function loadConfigFileExists() {
    $this->_obj->loadConfig('load_config_test.json');

    $this->assertEquals($this->_obj->getConfig('foo/bar'), 'baz');
  }

  /**
   * @covers Zend_Config_Json::setConfig
   * @covers Zend_Config_Json::getConfig
   * @covers Assembla_API::setConfig
   * @covers Assembla_API::getConfig
   *
   * @test
   */
  public function getSetConfig() {
    $api = new Assembla_API;

    $api->loadConfig('load_config_test.json');
    
    // Using setConfig on an already existing config key:
    // Loads config with foo/bar set to baz, sets it to test123
    // Then tests that its no longer equal to baz.
    $api->setConfig('foo/bar', 'test123');    

    $this->assertNotEquals($api->getConfig('foo/bar'), 'baz');

    // Using setConfig on a non-existent config key:
    $api->setConfig('a/b', 'abc');

    $this->assertEquals($api->getConfig('a/b'), 'abc');
  }
}