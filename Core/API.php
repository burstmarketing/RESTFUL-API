<?php

abstract class Core_API {

  protected $_use_cache = false;

  protected $_config;
  protected $_config_base_type;

  protected $_lastResponse;

  /**
   * FILTER RELATED FUNCTIONALITY
   **/

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

  public function clearFilters() {
    $this->_filters = array();
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


  /**
   * CONFIG RELATED FUNCTIONALITY
   **/

  public function loadConfig( $file ){
    if(is_readable( $file ) ){
      // skeleton code to add other config types
      // currently just defaults to json.
      switch( $this->_config_base_type ){
      default:
        $config_reader = new Zend\Config\Reader\Json();
        $this->_config = new Zend\Config\Config( $config_reader->fromFile( $file ), true );
      }

    } else {
      throw new Assembla_Exception( "Could not load file " . $file);
    }
  }

  public function getConfigs() {
    return $this->_config->toArray();
  }


  protected function _getConfig( &$config, $uri){
    $matches = explode( '/', $uri );
    if( count($matches) == 1 ) {
      return $config->$uri;
    }
    $next = array_shift( $matches );
    if( isset($config->$next) ){
      return $this->_getConfig( $config->$next, implode( $matches, "/" ));
    }
    return null;

  }


  public function getConfig( $uri ){
    return $this->_getConfig( $this->_config, $uri );
  }

  protected function _setConfig( &$config, $uri, $value ){
    $matches = explode( '/', $uri );
    if( count($matches) == 1 ) {
      $config->$uri = $value;
      return $this;
    }
    $next = array_shift( $matches );
    return $this->_setConfig( $config->$next, implode( $matches, "/" ), $value );
  }

  public function setConfig( $uri, $value ){
    return $this->_setConfig( $this->_config, $uri, $value );
  }


  /**
   * __CALL() RELATED FUNCTIONALITY
   **/

  protected function _underscore($name) {
    return strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
  }

  public function getClient( $args = false){
    return new Zend\Http\Client();
  }

  public function getLastResponse() {
    return $this->_lastResponse;
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


  public function __call($method, $args){
    $matches = array();

    if (preg_match('/^(load|post|put|delete)(.*)/', $method, $matches)) {
      $key = $this->_underscore($matches[2]);
      $uri_arguments = isset($args[0]) ? $args[0] : array();

      if( ($service = $this->getService($key) ) !== false) {

        $request = $service->validateArgs( $uri_arguments )->getRequest( $uri_arguments );
        $client = $this->getClient();

        if( isset($args[1] ) ){
          $request->manageRequestData( $args[1] );
        }

        $this->_lastResponse = $client->dispatch($request);

        return $this->_lastResponse->getObject( $service, $this->getFilters() );

      } else {
        throw new Assembla_Exception(sprintf('Service for %s could not be found.', $key));
      }


    } else {
      throw new Assembla_Exception('Invalid method. Not one of load/post/put/delete.');
    }
  }

}
