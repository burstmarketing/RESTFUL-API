<?php

if (!defined("ASSEMBLA_REST_API_ROOT")) {
  define("ASSEMBLA_REST_API_ROOT", realpath(dirname(__DIR__)));
 }

require_once ASSEMBLA_REST_API_ROOT . '/../Autoload.php';
require_once ASSEMBLA_REST_API_ROOT . '/../Assembla/API/V1/Response.php';



class Assembla_API_V1_ResponseTest extends PHPUnit_Framework_TestCase {

  public function setUp() {
  }

  public function testGetObject(){
    // Remove the following lines when you implement this test.
    $this->markTestIncomplete(
      'This test has not been implemented yet.'
    );

  }
}