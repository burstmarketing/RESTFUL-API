<?php

if (!defined("ASSEMBLA_REST_API_ROOT")) {
  define("ASSEMBLA_REST_API_ROOT", realpath(dirname(__DIR__)));
 }

require_once ASSEMBLA_REST_API_ROOT . '/../Autoload.php';
require_once ASSEMBLA_REST_API_ROOT . '/../Assembla/API.php';

// This needs to be moved out of here,  Assembla_API_ServiceTest.php Also depends on this object
/*
  class Mock_Assembla_API extends Assembla_API {

  protected function _getClient( $args = false){
  $client = $this->getMock('Assembla_API_Client');
  $client->setOptions( array("sslverifypeer" => false) );
  return $client;
  }

  }
*/


class Assembla_APITest extends PHPUnit_Framework_TestCase {

  protected $object;

  protected function _getMockedAPIClient($path){
    $client = $this->object->getClient();
    $adapter = new Zend\Http\Client\Adapter\Test;
    if ( !is_readable($path) ) {
      $this->markTestSkipped(sprintf('Fixture file unreadable: %s.', $path));
    } else {
      $adapter->setResponse( file_get_contents($path) );
      $client->setAdapter($adapter);
    }
    return $client;
  }

  protected function setUp() {
    $this->object = $this->getMock("Assembla_API", array("getClient"));
    $this->object->loadConfig(ASSEMBLA_REST_API_ROOT . '/../Assembla/etc/config.json');
  }


  public function testLoadActivity() {
    $this->object->expects($this->any())
      ->method('_getClient')
      ->will($this->returnValue($this->_getMockedAPIClient(ASSEMBLA_REST_API_ROOT . 'test/fixutures/http/load.activity.response' )));

    $activity_collection = $this->object->loadActivity();
    $this->assertInstanceOf('Assembla_Collection_Activity', $activity_collection);

    foreach ($activity_collection as $activity) {
      $this->assertInstanceOf('Assembla_Model_Activity', $activity);
    }

  }
  /*
  public function testLoadTickets() {
    $ticket_collection = $this->object->loadTickets(array('space_id' => 'foo'));
    $this->assertInstanceOf('Assembla_Collection_Ticket', $ticket_collection);

    foreach ($ticket_collection as $ticket) {
      $this->assertInstanceOf('Assembla_Model_Ticket', $ticket);
    }
  }

  public function testLoadTicketsSetFilters() {
    $this->object->addFilter(
      function($ticket) {
        return $ticket['status'] == 'New';
      }, array('New'));
    $ticket_collection = $this->object->loadTickets(array('space_id' => 'foo'));
    foreach ($ticket_collection as $ticket) {
      $this->assertInstanceOf('Assembla_Model_Ticket', $ticket);
      $this->assertEquals('New', $ticket->getStatus());
    }
  }

  public function testLoadTicketsFiltersCustomFields() {
    $ticket_collection = $this->object->loadTickets(array('space_id' => 'foo'));
    foreach ($ticket_collection as $ticket) {
      if ($ticket->get('custom_fields')) {
        $this->assertInstanceOf('Assembla_Collection_Ticket_Customfield', $ticket->getCustomFields());
      }
    }
  }

  public function testLoadTicketByNumber() {
    $ticket = $this->object->loadTicketByNumber(array('space_id' => 'foo', 'number' => 'bar'));
    $this->assertInstanceOf('Assembla_Model_Ticket', $ticket);
  }

  public function testLoadTicketById() {
    $ticket = $this->object->loadTicketById(array('space_id' => 'foo', 'id' => 'bar'));
    $this->assertInstanceOf('Assembla_Model_Ticket', $ticket);
  }

  public function testLoadSpaces() {
    $space_collection = $this->object->loadSpaces();
    $this->assertInstanceOf('Assembla_Collection_Space', $space_collection);

    foreach ($space_collection as $space) {
      $this->assertInstanceOf('Assembla_Model_Space', $space);
    }
  }

  public function testLoadSpace() {
    $space = $this->object->loadSpace(array('id' => 'foo'));
    $this->assertInstanceOf('Assembla_Model_Space', $space);
  }

  public function testLoadUser() {
    $user = $this->object->loadUser();
    $this->assertInstanceOf('Assembla_Model_User', $user);
  }

  public function testLoadShowUser() {
    $user = $this->object->loadShowUser(array('id_or_login' => 'foo'));
    $this->assertInstanceOf('Assembla_Model_User', $user);
  }

  public function testLoadUsersBySpace() {
    $users = $this->object->loadUsersBySpace(array('space_id' => 'foo'));
    $this->assertInstanceOf('Assembla_Collection_User', $users);
  }

  public function testLoadUserRoles() {
    $users = $this->object->loadUserRoles(array('space_id' => 'foo'));
    $this->assertInstanceOf('Assembla_Collection_Userrole', $users);
  }

  public function testLoadUserRole() {
    $user = $this->object->loadUserRole(array('space_id' => 'foo', 'id' => 'bar'));
    $this->assertInstanceOf('Assembla_Model_Userrole', $user);
  }

  public function testLoadSpaceTicketStatuses() {
    $statuses = $this->object->loadSpaceTicketStatuses(array('space_id' => 'foo'));
    $this->assertInstanceOf('Assembla_Collection_Ticket_Status', $statuses);
  }

  public function testLoadSpaceTicketStatusById() {
    $status = $this->object->loadSpaceTicketStatusById(array('space_id' => 'foo', 'id' => 'bar'));
    $this->assertInstanceOf('Assembla_Model_Ticket_Status', $status);
  }

  public function testProcessRequest() {
    $this->assertInstanceOf('Assembla_Collection_Activity', $this->object->loadActivity());
  }

  public function testLoadConfigReturnsItselfIfAnInstanceOfZendConfig() {
    $test_config = new Zend\Config\Config(array());

    $this->object->loadConfig($test_config);

    $this->assertAttributeEquals($test_config, '_config', $this->object);
  }
  */
  // Covered by Core_API test
  public function testLoadConfigForFiles() {}
}