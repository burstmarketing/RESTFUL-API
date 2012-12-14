<?php


abstract class Core_API {
  protected $_use_cache = false;

  protected $_config;
  protected $_config_base_type;

  public function useCache( $use_cache = null ){
    if( $use_cache === null ){
      return $this->_use_cache;
    }
    $this->_use_cache = $use_cache;
    return $this;
  }


  public function loadConfig( $file ){
        if(is_readable( $file ) ){
          // skeleton code to add other config types
          // currently just defaults to json.
          switch( $this->_config_base_type ){
          default:
                $this->_config = new Zend_Config_Json( $file, null, true );
          }

        } else {
          throw new Exception( "Could not load file " . $file);
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




  // NOTE: getConfig( 'service' . $key )  should probably be a function
  //       like getService( $key ) which returns a Core_Config_Service object
  //       which we can get all this info from instead of requiring this be
  //       returned as an array.

  public function __call($method, $args){
    $matches = array();
    if( preg_match( '/^(load|post|put|delete)(.*)/', $method, $matches ) ){
      $type = $matches[1];
      $key = $this->_underscore($matches[2]);

      try {

        $request = $this->_getRequest();

        // @td make sure 'setAPI' is set up as an abstract function on
        //     Core_API_Request

        $request->setAPI($this);

        $service = $this->getService( $key );
        $service->key = $key;

        if( ! isset( $service->url ) ){
          $service->url = $this->getConfig("defaults/url");
        } else {
          throw new Exception( "could not locate a 'url' to use for this service");
        }

        $request->generateRequest( $service, $args );


        $response = $this->_getResponse();

        if( isset($service->classname) ){
          return $response->processRequest( $request, $service->classname );
        }

        return $response->processRequest( $request );

      } catch (Core_Exception_Auth $e){
        //	throw new Core_Exception_Auth('Y U NO AUTHENTICATE',0,$e);
        throw $e;
      }
    }
  }

  abstract protected function _getRequest();
  abstract protected function _getResponse();

  }