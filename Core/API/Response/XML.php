<?php

class Core_API_Response_XML extends Core_API_Response{


  public function processRequest( $http_response, $classname = "Core_Object" ){
	$class = new $classname();
	$class->setData( json_decode(json_encode((array) simplexml_load_string($http_response)),1) );
      	return $class;
  }

  }

?>