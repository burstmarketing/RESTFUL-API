<?php

abstract class Core_API {

  protected $_use_cache = false;

  protected $_config;
  protected $_config_base_type;

  protected $_filters = array();

  /**
   * I feel like this is more justification that
   * a filter should be an object..
   **/
  private function _validateFilter(array $filter) {
    if (!array_key_exists('callback', $filter) ||
        !array_key_exists('args', $filter)) {
      throw new Assembla_Exception('Invalid filter format; filters require a callback and arguments.');
    } elseif (!is_callable($filter['callback'])) {
      throw new Assembla_Exception('Invalid filter callback; callback is not callable.');
    }

    return $this;
  }

  public function addFilter($callback, $args) {
    $filter = array('callback' => $callback,
                    'args'     => (array) $args);

    $this->_validateFilter($filter);

    $this->_filters[] = $filter;

    return $this;
  }

  public function getFilters(){
    return $this->_filters;
  }

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


  /**
   * Return an object that inherits from Core_API_Service, this should represent
   * the service data and have functions for validating arguments and ultimately
   * generating a request
   * @param string $key - the key used to identify the service in the config
   **/

  public function getService( $key ){
    if( $this->getConfig( 'services/' . $key ) ){
      $service_config = $this->getConfig( 'services/' . $key );

      // @todo - maybe we should extend zend_config's __get() method to
      //         automatically check the 'defaults' section of the config
      //         if a config key isn't set on service?

      $service_object_class = "";
      if (isset($service_config->service_object) ) {
        $service_object_class = $service_config->service_object;
      } else {
        $service_object_class = $this->getConfig('defaults/service_object');
      }

      if(class_exists( $service_object_class )) {
        $service = new $service_object_class($this);
      } else {
        throw new Assembla_Exception(sprintf('Could not locate a Service Object for service %s.', $service->key) );
      }

      $service->setData( $service_config->toArray() );
      $service->setKey( $key );

      return $service;

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



  /**
   * Refactoring Note
   *
   *  __call($method, $args){
   *
   *   $request = $service->getRequest() :: implement a service object that returns a 'request'
   *   $response = $request->send() :: needs to return a response object with all the headers etc set on it
   *   $object = $response->processRequest() :: put switch statement in here so it can actually manage transfering shit
   *   return $object (Assembla_Collection_Ticket etc)
   * }
   *
   *  request/response objects should inherit from Zend_Http_Reqest/Response objects
   **/





  // NOTE: getConfig( 'service' . $key )  should probably be a function
  //       like getService( $key ) which returns a Core_Config_Service object
  //       which we can get all this info from instead of requiring this be
  //       returned as an array.

  public function __call($method, $args){
    $matches = array();

    if (preg_match('/^(load|post|put|delete)(.*)/', $method, $matches)) {
      $key = $this->_underscore($matches[2]);

      $uri_arguments = isset($args[0]) ? $args[0] : array();

      // Clone so service doesn't retain values from last call
      // @todo - Shouldn't this check for a service that has a GET/POST/PUT/DELETE value corresponding to $key?
      if( ($service = $this->getService($key) ) === false) {
        throw new Assembla_Exception(sprintf('Service for %s could not be found.', $key));
      }


      $request = $service->validateArgs( $uri_arguments )->getRequest( $uri_arguments );
      $request->setCurlData( isset($args[1]) ? $args[1] : null );

      $response = $request->send();

      // this needs to be refactored so that "send" returns a response object which gets filters and then "processesRequest"

      return $this->_getResponse()
        ->setFilters($this->getFilters())
        ->processRequest($request, $service->getClassname() );
    } else {
      throw new Assembla_Exception('Invalid method. Not one of load/post/put/delete.');
    }
  }

  abstract protected function _getRequest();
  abstract protected function _getResponse();

}
