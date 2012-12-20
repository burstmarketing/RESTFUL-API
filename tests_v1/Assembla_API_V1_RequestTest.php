<?php

if (!defined("ASSEMBLA_REST_API_ROOT")) {
  define("ASSEMBLA_REST_API_ROOT", realpath(dirname(__DIR__)));
}

require_once ASSEMBLA_REST_API_ROOT . '/Autoload.php';
require_once ASSEMBLA_REST_API_ROOT . '/Assembla/API/V1/Request.php';


class Dummy_API_V1_Request extends Assembla_API_V1_Request {

  public function exposedProcessHeader($arg) {
    return $this->_processHeader($arg);
  }

  public function exposedProcessUri($uri, array $args=array()) {
    return $this->_processURI($uri, $args);
  }

  public function exposedGetUriArgs($uri, array $args) {
    return $this->_getURIArgs($uri, $args);
  }
}

class Assembla_API_V1_RequestTest extends PHPUnit_Framework_TestCase {

  protected $_request;
  protected $_api;

  public function setUp() {
    $this->_api = new Assembla_API;
    $this->_api->loadConfig(ASSEMBLA_REST_API_ROOT . '/Assembla/etc/config.json');
    $this->_request = new Dummy_API_V1_Request;
    $this->_request->setAPI($this->_api);
  }

  public function testProcessHeaderReturnsWithNoVariables() {
    $this->assertEquals('example', $this->_request->exposedProcessHeader('example'));
  }

  public function testProcessHeaderReplacesVariables() {
    $this->assertEquals($this->_request->exposedProcessHeader('${defaults/url}'), 'https://api.assembla.com');
  }

  public function testProcessUriReturnsUnchangedIfNoArgs() {
    // Test it will always return the same URI when $args is empty
    $this->assertEquals('test-1', $this->_request->exposedProcessUri('test-1'));
    $this->assertEquals('test-2', $this->_request->exposedProcessUri('test-2'));
  }

  public function testProcessUriReplacesVariablesInArgs() {
    // Test basic replacement
    $this->assertEquals('a-b-c', $this->_request->exposedProcessUri('a-${example}-c', array('example' => 'b')));
  }

  public function testProcessUriThrowsExceptionForMissingProperty() {
    $this->setExpectedException('Assembla_Exception');

    $this->_request->exposedProcessUri('a-${test}-c', array('not-test' => 'b'));
  }

  public function test_getURIArgs() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }

  public function testGenerateRequest() {
    $this->markTestIncomplete('This test has not been implemented yet.');
  }
}
