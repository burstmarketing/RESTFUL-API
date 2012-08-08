<?php
class Assembla_API_Response  extends Core_API_Response_XML {
  
  public function processRequest( $request, $classname = "Core_Object" ){
	$http_response = $request->send();	

	if(substr($http_response, 0, 2) == "<?") {
	  switch( $request->getType() ){
	  case 'PUT':
	  case 'POST':
	  case 'DELETE':
	    $message = new Core_Object;
	  
	    if( simplexml_load_string($http_response) ){		
	      $element = new Assembla_API_XML_Element( $http_response );
	      $message->setSuccess(1)
		->setBody( $element->asArray() );
	      return $message;
		
	    } else {
	      $message->setSuccess(0)
		->setBody( $http_response );
	      return $message;
	    }
	  
	    break;
	  default:
	    $class = new $classname();	
	    if( simplexml_load_string($http_response) ){
	      $element = new Assembla_API_XML_Element( $http_response );
	      return $class->load( $element );
	    } else {
	      return $class->setLoadError( $http_response );
	    }
	    break;
	  }

	} else {
	  throw new Core_Exception_Auth('Failed to authenticate with Assembla');
	}
  }
  
}

?>