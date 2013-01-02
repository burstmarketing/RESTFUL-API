<?php

if (!defined("ASSEMBLA_REST_API_ROOT")) {
  define("ASSEMBLA_REST_API_ROOT", realpath(dirname(__DIR__)));
}

require_once ASSEMBLA_REST_API_ROOT . '/../Autoload.php';
require_once ASSEMBLA_REST_API_ROOT . '/../Assembla/API/V1/Response.php';

class Mock_Assembla_API extends Assembla_API {

  protected function _getRequest() {
    return new Mock_Assembla_API_V1_Request;
  }
}

class Mock_Assembla_API_V1_Request extends Assembla_API_V1_Request {

  public $uri_fixture_mapping = array(
    '/v1/activity.json' => '/fixtures/activity.json');

  /**
   * If it has a URI, it exists in the fixture mapping, and it's readable,
   * it returns the fixtures contents, otherwise it throws an error and marks
   * the test as skipped.
   **/
  public function send() {
    if ($uri = $this->getUri()) {
      if (!array_key_exists($uri, $this->uri_fixture_mapping)) {
	$this->markTestSkipped(sprintf('No Fixture found for URI: %s.', $uri));
      } elseif (!is_readable(ASSEMBLA_REST_API_ROOT . '/../tests' . $this->uri_fixture_mapping[$uri])) {
	$this->markTestSkipped(sprintf('Fixture file unreadable: %s.', $this->uri_fixture_mapping[$uri]));
      } else {
	return file_get_contents(ASSEMBLA_REST_API_ROOT . '/../tests' . $this->uri_fixture_mapping[$uri]);
      }
    } else {
      $this->markTestSkipped('No URI found on Request Object.');
    }
  }
}

class Assembla_API_V1_ResponseTest extends PHPUnit_Framework_TestCase {

  public function setUp() {
    $this->api = new Mock_Assembla_API;
    $this->api->loadConfig(ASSEMBLA_REST_API_ROOT . '/../Assembla/etc/config.json');
  }

  public function testLoadActivity() {
    $activity_collection = $this->api->loadActivity();

    $this->assertInstanceOf('Assembla_Collection_Activity', $activity_collection);

    foreach ($activity_collection as $activity) {
      $this->assertInstanceOf('Assembla_Model_Activity', $activity);
    }
  }

  public function testProcessRequest() {
    $this->assertInstanceOf('Assembla_Collection_Activity', $this->api->loadActivity());
  }
}