<?php

class Assembla_API_V1_Request extends Core_API_Request_Json {

  public $variable_pattern = '/\$\{([^\$}]+)\}/';

  protected $_api;

  public function getAPI(){
    return $this->_api;
  }

  public function setAPI(Core_API &$api) {
    $this->_api = &$api;
    return $this;
  }

  protected function _processHeader( $header ){
    $api = $this->getAPI();

    $callback = function($matches) use ($api) {
      $uri = $matches[1];
      return $api->getConfig($uri);
    };

    return preg_replace_callback($this->variable_pattern, $callback, $header);
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

  public function addHeader( $header ){
    $header = $this->_processHeader( $header );
    parent::addHeader( $header );
  }

  public function validateArgs(Zend_Config $service, $args) {
    $args    = (is_array($args)) ? $args : array();
    $matches = array();

    if (count($args) != preg_match_all($this->variable_pattern, $service->uri, $matches)) {
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

  /**
   * Sets service variables onto the request object ($this).
   * Sets up URI/Datatype
   * Sets up headers.
   **/
  public function generateRequest(Zend_Config $service, array $args) {
    $this->setKey($service->key)
         ->setUrl($service->url);

    if (isset($service->uri)) {
      $this->_setupUri($service, $args);
      $this->_setupDatatype($service);
    } else {
      throw new Assembla_Exception(sprintf('Can\'t find a URI for %s.', $service->key));
    }

    if (isset($service->type)) {
      $this->setType($service->type);
    } else {
      throw new Assembla_Exception(sprintf('Can\'t find type for %s.', $service->key));
    }

    $this->_setupHeaders($service);

    return $this;
  }

  protected function _setupUri(Zend_Config $service, array $args) {
    $args = (is_array($args) && isset($args[0])) ? $args[0] : array();

    $this->setUri($this->_processURI($service->uri, $args));

    if (isset($args[1]) && is_string($args[1])) {
      $this->setCurlData($args[1]);
    }

    return $this;
  }

  /**
   * Due to V1 API appending .json or .xml to the end of requests,
   * query strings at the end of URIs in the config break things, as they
   * end up being uri?query=arg.json when they should be
   * uri.json?query=arg.
   * This checks for that, and replaces appropriately, and if there is no query
   * string, just throws .datatype on the end.
   **/
  protected function _setupDatatype(Zend_Config $service) {
    if ($service->datatype) {
      $this->setDatatype($service->datatype);

      if (preg_match('/(.*)\?(.*)/', $this->getUri())) {
        $this->setUri(preg_replace('/(.*)\?(.*)/', '$1.' . $service->datatype . '?$2', $this->getUri()));
      } else {
        $this->setUri($this->getUri() . '.' . $service->datatype);
      }
    }

    return $this;
  }

  protected function _setupHeaders(Zend_Config $service) {
    if ($service->headers) {
      foreach ($service->headers as $header) {
        $this->addHeader($header);
      }
    }

    return $this;
  }
}