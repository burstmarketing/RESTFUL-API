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
                                      '/v1/activity.json' => '/fixtures/activity.json',
                                      '/v1/spaces.json' => '/fixtures/spaces.json',
                                      '/v1/spaces/foo/tickets.json' => '/fixtures/tickets.json',
                                      '/v1/user.json' => '/fixtures/user.json',
                                      '/v1/users/foo.json' => '/fixtures/user.json',
                                      '/v1/spaces/foo/users.json' => '/fixtures/spaces.json',
                                      '/v1/spaces/foo.json' => '/fixtures/space.json',
                                      '/v1/spaces/foo/user_roles.json' => '/fixtures/user_roles.json',
                                      '/v1/spaces/foo/user_roles/bar.json' => '/fixtures/user_roles_bar.json',
                                      '/v1/spaces/foo/tickets/bar.json' => '/fixtures/ticket.json',
                                      '/v1/spaces/foo/tickets/id/bar.json' =>'/fixtures/ticket.json',
                                      '/v1/spaces/foo/tickets/statuses.json' => '/fixtures/ticketstatuses.json',
                                      '/v1/spaces/foo/tickets/statuses/bar.json' => '/fixtures/ticketstatuses.json',

                                      );

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

  public function testLoadTickets() {
    $ticket_collection = $this->api->loadTickets(array('space_id' => 'foo'));
    $this->assertInstanceOf('Assembla_Collection_Ticket', $ticket_collection);

    foreach ($ticket_collection as $ticket) {
      $this->assertInstanceOf('Assembla_Model_Ticket', $ticket);
    }
  }

  public function testLoadTicketsSetFilters() {
    $this->api->addFilter(
                          function($ticket) {
                            return $ticket['status'] == 'New';
                              }, array('New'));
    $ticket_collection = $this->api->loadTickets(array('space_id' => 'foo'));
    foreach ($ticket_collection as $ticket) {
      $this->assertInstanceOf('Assembla_Model_Ticket', $ticket);
      $this->assertEquals('New', $ticket->getStatus());
    }
  }

  public function testLoadTicketsFiltersCustomFields() {
    $ticket_collection = $this->api->loadTickets(array('space_id' => 'foo'));
    foreach ($ticket_collection as $ticket) {
      if ($ticket->get('custom_fields')) {
        $this->assertInstanceOf('Assembla_Collection_Ticket_Customfield', $ticket->getCustomFields());
      }
    }
  }

  public function testLoadTicketByNumber() {
    $ticket = $this->api->loadTicketByNumber(array('space_id' => 'foo', 'number' => 'bar'));
    $this->assertInstanceOf('Assembla_Model_Ticket', $ticket);
  }

  public function testLoadTicketById() {
    $ticket = $this->api->loadTicketById(array('space_id' => 'foo', 'id' => 'bar'));
    $this->assertInstanceOf('Assembla_Model_Ticket', $ticket);
  }

  public function testLoadSpaces() {
    $space_collection = $this->api->loadSpaces();
    $this->assertInstanceOf('Assembla_Collection_Space', $space_collection);

    foreach ($space_collection as $space) {
      $this->assertInstanceOf('Assembla_Model_Space', $space);
    }
   }

  public function testLoadSpace() {
    $space = $this->api->loadSpace(array('id' => 'foo'));
    $this->assertInstanceOf('Assembla_Model_Space', $space);
  }

  public function testLoadUser() {
    $user = $this->api->loadUser();
    $this->assertInstanceOf('Assembla_Model_User', $user);
  }

  public function testLoadShowUser() {
    $user = $this->api->loadShowUser(array('id_or_login' => 'foo'));
    $this->assertInstanceOf('Assembla_Model_User', $user);
  }

  public function testLoadUsersBySpace() {
    $users = $this->api->loadUsersBySpace(array('space_id' => 'foo'));
    $this->assertInstanceOf('Assembla_Collection_User', $users);
  }

  public function testLoadUserRoles() {
    $users = $this->api->loadUserRoles(array('space_id' => 'foo'));
    $this->assertInstanceOf('Assembla_Collection_Userrole', $users);
  }

  public function testLoadUserRole() {
    $user = $this->api->loadUserRole(array('space_id' => 'foo', 'id' => 'bar'));
    $this->assertInstanceOf('Assembla_Model_Userrole', $user);
  }

  public function testLoadSpaceTicketStatuses() {
    $statuses = $this->api->loadSpaceTicketStatuses(array('space_id' => 'foo'));
    $this->assertInstanceOf('Assembla_Collection_Ticket_Status', $statuses);
  }

  public function testLoadSpaceTicketStatusById() {
    $status = $this->api->loadSpaceTicketStatusById(array('space_id' => 'foo', 'id' => 'bar'));
    $this->assertInstanceOf('Assembla_Model_Ticket_Status', $status);
  }

  public function testProcessRequest() {
    $this->assertInstanceOf('Assembla_Collection_Activity', $this->api->loadActivity());
  }
}