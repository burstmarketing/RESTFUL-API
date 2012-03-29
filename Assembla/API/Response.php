<?php
class Assembla_API_Response  extends Core_API_Response_XML {
  
  public function processRequest( $http_response, $classname = "Core_Object" ){
		$class = new $classname();

		if( simplexml_load_string($http_response) ){
		  $element = new Assembla_API_XML_Element( $http_response );
		  return $class->load( $element );
		} else {
		  return $class->setLoadError( $http_response );
		}

  }

  }

?>