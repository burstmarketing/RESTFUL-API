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
      throw new Assembla_Exception( "Could not load file " . $file);
    }
  }

  public function getConfigs() {
    return $this->_config->getConfigs();
  }

  // @todo - This will throw  a fatal error if $uri doesn't exist, and has a slash in it
  public function getConfig( $uri ){
    if ($config = $this->_config->getConfig($uri)) {
      return $config;
    } else {
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

    if (preg_match('/^(load|post|put|delete)(.*)/', $method, $matches)) {
      $key = $this->_underscore($matches[2]);

      $request = $this->_getRequest()
                      ->setAPI($this);

      // Clone so service doesn't retain values from last call
      if ($service = $this->getService($key)) {
        $service = clone $service;
        $service->key = $key;
      } else {
        throw new Assembla_Exception(sprintf('Service for %s could not be found.', $key));
      }

      // If a URL isn't set in the service definition, try to pull
      // a default from the config, if that fails, throw an exception.
      if ((!isset($service->url)) &&
          (!($service->url = $this->getConfig('defaults/url')))) {
        throw new Exception(sprintf('Could not locate a URL for service %s.', $service->key));
      }

      $request->generateRequest( $service, $args );

      return $this->_getResponse()
                  ->processRequest($request, (isset($service->classname)) ? $service->classname : '');
    } else {
      throw new Assembla_Exception('Invalid method. Not one of load/post/put/delete.');
    }
  }

  abstract protected function _getRequest();
  abstract protected function _getResponse();

}

class Assembla_Exception extends Exception {
  public function __construct($message = null, $code = 0, Exception $previous = null) {
    parent::__construct($message, $code, $previous);
  }
}