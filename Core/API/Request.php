<?php

class Core_API_Request extends Core_Object {


  protected function _parseVars( $uri, $args = array() ){

	if( empty($args) ):
	  return $uri;
	endif;          

	$callback = function($matches) use ($args) {
	  $propertyName = $matches[1];
	  if ( !array_key_exists( $propertyName, $args)) {
		throw new Exception( $propertyName . " not passed into _parseVars function. ");
	  }
	  
	  $propertyValue = $args[$propertyName];
	  
	  if (is_bool($propertyValue)) {
		if ($propertyValue === true) {
		  $propertyValue = "true";
		} else {
		  $propertyValue = "false";
		}
	  }	  
	  return $propertyValue;	  
	};


	while (strpos($uri, '${') !== false) {
	  $uri = preg_replace_callback('/\$\{([^\$}]+)\}/', $callback, $uri);
	}
	return $uri;

  }

  public function send(){

  }

?>