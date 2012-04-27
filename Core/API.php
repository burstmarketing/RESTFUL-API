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
		$this->_config = new Zend_Config_Json( RESTFUL_API_LOADER::getBaseDir() . $file, null, true );
	  }

	} else {
	  throw new Exception( "Could not load file " . RESTFUL_API_LOADER::getBaseDir() . $file);
	}
  }

  public function getConfigs() {
    return $this->_config->getConfigs();
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


  public function getService( $key ){
	
	if( $this->getConfig( 'services/' . $key ) ){
	  return $this->getConfig( 'services/' . $key );
	}

	return false;
	
  }


  //NOTE: all of this stuff should probably be refactored into
  //      a Core_RESTFUL_API class so we can use the rest of the framework
  //      for stuff like SOAP APIs. __call will have to be changed to call
  //      an abstract function so we set up the meat of the current function
  //      in our new Core_RESTFUL_API class.

  protected function _underscore($name) {
	return strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
  }

  protected function _processURI( $uri, $args = array() ){

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

  protected function _getURIArgs( $uri, $args ){
	$matches = array();
	preg_match_all('/\$\{([^\$}]+)\}/', $uri, $matches);

	if( isset($matches[1]) ){
	  $vals = array_slice( $args, 0, count($matches[1]) );
	  return array_combine( $matches[1], $vals );
	}

	return array(); 
  }


  // NOTE: getConfig( 'service' . $key )  should probably be a function
  //       like getService( $key ) which returns a Core_Config_Service object
  //       which we can get all this info from instead of requiring this be
  //       returned as an array.

  public function __call($method, $args){

	if(substr($method, 0, 4) == "load"){
	  $key = $this->_underscore(substr($method,4));
	  try {
		
		if( ($service = $this->getService( $key )) !== null ){

		  $request = $this->_getRequest();
		  $request->setKey( $key );

		  $request = $this->_preProcessRequest($request);


		  if( $this->getConfig("defaults/url") ){
			$request->setUrl( $this->getConfig("defaults/url") );
		  }

		  if( isset( $service->uri ) ){
			
			// Handle $args - this can be passed in as a 
			// single array with key => value pairs to be
			// sent to processURI  or $args can just be a
			// list of arguments in wich case we add the
			// keys to the arguments with _getURIArgs()
			// NOTE: this should probably be reworked to simply
			//       validate the arguments and set the uri
			//       in a protected function.
			if( ! empty( $args ) ){
			  if( is_array( $args[0] ) ){
				$_args = $args[0];
			  } else {
				$_args =  $this->_getURIArgs( $service->uri, $args );
			  }				
			  $request->setUri( $this->_processURI( $service->uri, $_args ) );
			} else {
			  // no arguments,  just set the uri.
			  $request->setUri( $service->uri );
			}
			
			// set curl data on this request
			if( isset($args[1]) && is_string( $args[1] ) ){
			  $request->setCurlData( $args[1] );
			}				

		  } else {
			throw new Exception("could not find 'uri' in service: " . $key );
		  }
		  
		  if( isset( $service->type ) ){
			$request->setType( $service->type );
		  } else {
			throw new Exception("type is not defined in service: " . $key );
		  }
		  
		  $request = $this->_postProcessRequest( $request );
		  
		  $response = $this->_getResponse();

		  if( isset($service->classname) ){
			return $response->processRequest( $request, $service->classname );
		  }		  		  		  

		  return $response->processRequest( $request );

		} else {
		  throw new Exception( 'no service set at: services/' . $key );
		}

	  } catch (Exception $e){
		throw $e;
	  }
	}
  }

  abstract protected function _getRequest();
  abstract protected function _getResponse();
  abstract protected function _preProcessRequest( Core_API_Request $request );
  abstract protected function _postProcessRequest( Core_API_Request $request);

  }