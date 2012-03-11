<?php


abstract class Core_API {
  protected $_config;
  protected $_config_base_type;

  public function loadConfig( $file ){
	if(is_readable(  RESTFUL_API_LOADER::getBaseDir() . $file ) ){
	  // skeleton code to add other config types
	  // currently just defaults to json.
	  switch( $this->_config_base_type ){
	  default:
		$this->_config = new Core_Config_JSON;
	  }

	  $this->_config->load( RESTFUL_API_LOADER::getBaseDir() . $file );
	} else {
	  throw new Exception( "Could not load file " . RESTFUL_API_LOADER::getBaseDir() . $file);
	}
  }

  public function getConfig( $uri ){
	try {
	  return $this->_config->getConfig( $uri );
	} catch (Exception $e ) {
	  return false;
	}
  }

  public function setConfig( $uri, $value ){
	$this->_config->setConfig($uri, $value );
	return $this;
  }





  protected function _underscore($name) {
	return strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
  }

  protected function _parseUri( $uri, $args = array() ){

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

  public function __call($method, $args){

	if(substr($method, 0, 4) == "load"){
	  $key = $this->_underscore(substr($method,4));
	  try {
		
		if( $this->getConfig( 'services/' . $key ) !== false ){
		  
		  
		  $request = $this->_processRequest( $this->_getRequest() );
		  $response = $request->send();


		} else {
		  throw new Exception( 'no service set at: services/' . $key );
		}

	  } catch (Exception $e){
		throw $e;
	  }
	}
  }

  abstract protected function _getRequest();
  abstract protected function _processRequest( Core_API_Request $request );

  }