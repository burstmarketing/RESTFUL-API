<?php
if (!defined("ASSEMBLA_REST_API_ROOT")) {
  define("ASSEMBLA_REST_API_ROOT", realpath(dirname(__DIR__)));
}

require_once ASSEMBLA_REST_API_ROOT . '/../Autoload.php';
require_once ASSEMBLA_REST_API_ROOT . '/../Assembla/API/Client.php';




class Mock_Assembla_API_Client extends Assembla_API_Client {

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


  public function send(Request $request = null){
    if ($uri = $request->getUri()) {
      if (!array_key_exists($uri, $this->uri_fixture_mapping)) {
        $this->markTestSkipped(sprintf('No Fixture found for URI: %s.', $uri));
      } elseif (!is_readable(ASSEMBLA_REST_API_ROOT . '/../tests' . $this->uri_fixture_mapping[$uri])) {
        $this->markTestSkipped(sprintf('Fixture file unreadable: %s.', $this->uri_fixture_mapping[$uri]));
      } else {
        $this->_lastResponse = file_get_contents(ASSEMBLA_REST_API_ROOT . '/../tests' . $this->uri_fixture_mapping[$uri]);
      }
    }
  }

}


class Assembla_API_ClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Assembla_API_Client
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Assembla_API_Client;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Assembla_API_Client::dispatch
     * @todo   Implement testDispatch().
     */
    public function testDispatch()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
