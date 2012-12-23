<?php

class Core_API_Response_Json extends Core_API_Response {

  public function processRequest(Core_API_Request $http_response, $classname='Core_Object') {
    $class = new $classname;
    $class->setData(json_decode((array)$http_response, true));

    return $class;
  }
}