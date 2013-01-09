<?php

if (!defined("ASSEMBLA_REST_API_ROOT")) {
  define("ASSEMBLA_REST_API_ROOT", realpath(dirname(__DIR__)));
 }

require_once ASSEMBLA_REST_API_ROOT . '/../Autoload.php';
require_once ASSEMBLA_REST_API_ROOT . '/../Assembla/API/V1/Request.php';

class Dummy_API_V1_Request extends Assembla_API_V1_Request {}

class Assembla_API_V1_RequestTest extends PHPUnit_Framework_TestCase {

  protected $_request;
  protected $_api;

  public function setUp() {
    $this->_api = new Assembla_API;
    $this->_api->loadConfig(ASSEMBLA_REST_API_ROOT . '/../Assembla/etc/config.json');
    $this->_request = new Dummy_API_V1_Request;
    $this->_request->setAPI($this->_api);

    // Setup protected or private methods we may need to test
    $this->processHeader = new ReflectionMethod('Assembla_API_V1_Request', '_processHeader');
    $this->processHeader->setAccessible(true);

    $this->processURI = new ReflectionMethod('Assembla_API_V1_Request', '_processURI');
    $this->processURI->setAccessible(true);
  }

  public function testProcessHeaderReturnsWithNoVariables() {
    $this->assertEquals('example', $this->processHeader->invoke($this->_request, 'example'));
  }

  public function testProcessHeaderReplacesVariables() {
    $this->assertEquals($this->processHeader->invoke($this->_request, '${defaults/url}'), 'https://api.assembla.com');
  }

  public function testProcessUriReturnsUnchangedIfNoArgs() {
    // Test it will always return the same URI when $args is empty
    $this->assertEquals('test-1', $this->processURI->invoke($this->_request, 'test-1'));
    $this->assertEquals('test-2', $this->processURI->invoke($this->_request, 'test-2'));
  }

  public function testProcessUriReplacesVariablesInArgs() {
    // Test basic replacement
    $this->assertEquals('a-b-c', $this->processURI->invoke($this->_request, 'a-${example}-c', array('example' => 'b')));
  }

  public function testProcessUriThrowsExceptionForMissingProperty() {
    $this->setExpectedException('Assembla_Exception',
                                "test not passed into _parseVars function. ");

    $this->processURI->invoke($this->_request, 'a-${test}-c', array('not-test' => 'b'));
  }

  public function testAddHeaderProcessesHeader() {
    $this->_request->addHeader('${test}');
    $this->refObject = new ReflectionClass($this->_request);
    $this->_data = $this->refObject->getProperty('_data');
    $this->_data->setAccessible(true);
    $this->_data = $this->_data->getValue($this->_request);
    $testArray = array("headers" => array(''));
    $this->assertEquals($testArray, $this->_data);
  }

  public function testValidateArgsThrowsExceptionForInvalidArgumentCount() {
    $service = new Zend_Config(array('uri' => '${one_arg}'), true);

    $this->setExpectedException('Assembla_Exception',
                                'Argument count doesn\'t match the services argument count.');

    // Should only have one arg
    $this->_request->validateArgs($service, array('two', 'args'));
  }

  public function testValidateArgsThrowsExceptionForInvalidArgumentKeys() {
    $service = new Zend_Config(array('uri' => '${one_arg}/${second_arg}'), true);

    $this->setExpectedException('Assembla_Exception',
                                'Arguments expected vs arguments received do not match.');

    // Should be second_arg instead of two_arg
    $this->_request->validateArgs($service, array('one_arg' => 'value',
                                                  'two_arg' => 'value'));
  }

  public function testGenerateRequestThrowsExceptionForNoUri() {
    $service = new Zend_Config(array('key' => 'sample'), true);

    $this->setExpectedException('Assembla_Exception',
                                'Can\'t find a URI for sample.');

    $this->_request->generateRequest($service, array());
  }

  public function testGenerateRequestThrowsExceptionForNoType() {
    $service = new Zend_Config(array('uri' => 'some-uri',
                                     'key' => 'sample'), true);

    $this->setExpectedException('Assembla_Exception',
                                'Can\'t find type for sample.');

    $this->_request->generateRequest($service, array());
  }
}
