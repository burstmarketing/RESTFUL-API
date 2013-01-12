<?php

abstract class Core_API_Response extends Zend\Http\Response {

  abstract public function processRequest(Core_API_Request  $http_response, $classname = "Core_Object" );

}