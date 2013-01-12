<?php

class Assembla_API_Service extends Core_API_Service {
  protected $_api;
  public $variable_pattern = '/\$\{([^\$}]+)\}/';

  public function __construct() {
    $args = func_get_args();

    if (empty($args[0]) || !( $args[0] instanceof Core_API )) {
      throw new Assembla_Exception( __CLASS__ . " must be instantiated with an object of type Core_API!");
    }

    $this->_api = &$args[0];
    $this->_data = array();

  }

  public function __call($method, $args) {
    switch (substr($method, 0, 3)) {
    case 'get' :
      $key = $this->_underscore(substr($method,3));

      // if we can't get it off the actual service _data[]  then see
      // if its in the API's default section. This couples Service
      // objects to their API object,  but i don't think that will prove
      // to be an issue - Chris
      if( ($data = parent::__call($method, $args )) === null ){
        $data = $this->getAPI()->getConfig('defaults/' . $key);
      }
      return $data;

    default :
      return parent::__call($method, $args);
    }
  }

  public function getAPI(){
    return $this->_api;
  }


  public function validateArgs($args) {
    $args    = (is_array($args)) ? $args : array();
    $matches = array();

    if (count($args) != preg_match_all($this->variable_pattern, $this->getUri(), $matches)) {
      throw new Assembla_Exception('Argument count doesn\'t match the services argument count.');
    } else {
      // We have an array of arguments with the same count of
      // the amount of expected arguments, validate the keys are the same.
      $args_expected = $matches[1];
      $args_received = array_keys($args);

      sort($args_received);
      sort($args_expected);

      if (array_diff($args_expected, $args_received)) {
        throw new Assembla_Exception('Arguments expected vs arguments received do not match.');
      }
    }

    return $this;
  }

  public function getRequest( $args ) {
    $request_class_name = $this->getRequestClassName();
    $request = new $request_class_name();

    if( $this->getUri() ){
      $request->setUri( $this->getUrl() . $this->processUri( $args ) );
    } else {
      throw new Assembla_Exception(sprintf('Can\'t find a URI for %s.', $service->key));
    }
    $request->setMethod( $this->getType() );
    $request->getHeaders()->addHeaders( $this->_setupHeaders() );

    return $request;
  }


  /*
  public function getRequest( $args ){
    $request_class_name = $this->getRequestClassName();
    $request = new $request_class_name();

    $request->setService( $this );

    if( $this->getUri() ){
      $request->setUri( $this->processUri( $args ) );
    } else {
      throw new Assembla_Exception(sprintf('Can\'t find a URI for %s.', $service->key));
    }
    $request->setType( $this->getType() );
    $request->setUrl( $this->getUrl() );

    $request->setAPI( $this->getAPI() );
    $request->setHeaders( $this->_setupHeaders() );


    return $request;
  }
  */

  public function processUri( $args ) {
    return $this->_setupDatatype( $this->_processURI($this->getUri(), $args) );
  }

  protected function _processURI( $uri, array $args = array() ){
    if (empty($args)) {
      return $uri;
    }

    $callback = function($matches) use ($args) {
      $propertyName = $matches[1];

      if ( !array_key_exists( $propertyName, $args)) {
        throw new Assembla_Exception( $propertyName . " not passed into _parseVars function. ");
      }

      $propertyValue = $args[$propertyName];

      if (is_bool($propertyValue)) {
        return ($propertyValue === true) ? 'true' : 'false';
      } else {
        return $propertyValue;
      }
    };

    return preg_replace_callback($this->variable_pattern, $callback, $uri);
  }



  /**
   * Due to V1 API appending .json or .xml to the end of requests,
   * query strings at the end of URIs in the config break things, as they
   * end up being uri?query=arg.json when they should be
   * uri.json?query=arg.
   * This checks for that, and replaces appropriately, and if there is no query
   * string, just throws .datatype on the end.
   **/

  protected function _setupDatatype( $uri ) {
    if( $this->getDatatype() ){
      if (preg_match('/(.*)\?(.*)/', $uri )) {
        return preg_replace('/(.*)\?(.*)/', '$1.' . $this->getDatatype() . '?$2', $uri );
      }
      return $uri . "." . $this->getDatatype();
    }
    return $uri;
  }

  protected function _setupHeaders() {
    $processed_headers = array();
    if( $this->getHeaders() ) {
      foreach( $this->getHeaders() as $header ){
        $processed_headers[] = $this->_processHeader( $header );
      }
    }
    return $processed_headers;
  }

  protected function _processHeader( $header ){
    $api = $this->getAPI();
    $callback = function($matches) use ($api) {
      $uri = $matches[1];
      return $api->getConfig($uri);
    };

    return preg_replace_callback($this->variable_pattern, $callback, $header);
  }


}