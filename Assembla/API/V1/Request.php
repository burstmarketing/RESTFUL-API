<?php

class Assembla_API_V1_Request extends Core_API_Request_Json {

  protected $_api;
  protected $_service;

  public function getResponseClassName(){
    if( $this->getService() instanceof Core_API_Service ){
      return $this->getService()->getResponseClassName();
    }
    return parent::getResponseClassName();
  }

  public function getService(){
    return $this->_service;
  }

  public function setService(Core_API_Service $service) {
    $this->_service = $service;
    return $this;
  }

  public function setAPI( Core_API &$api ){
    $this->_api = $api;
    return $this;
  }

  public function getAPI(){
    if( $this->getService() instanceof Core_API_Service ){
      return $this->getService()->getAPI();
    }
    return $this->_api;
  }

  /*
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
  */
  /*
  public function addHeader( $header ){
    $header = $this->_processHeader( $header );
    parent::addHeader( $header );
  }
  */


  /**
   * Sets service variables onto the request object ($this).
   * Sets up URI/Datatype
   * Sets up headers.
   **/
  /*
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
  */
  /*
  protected function _setupUri(Zend_Config $service, array $args) {
    $this->setUri($this->_processURI($service->uri, (array) current($args)));

    $post_or_put_args = (isset($args[1])) ? $args[1] : false;

    if ($post_or_put_args) {
        if ($service->datatype == 'json' && is_array($post_or_put_args)) {
            $post_or_put_args = json_encode($post_or_put_args);
        }

        $this->setCurlData($post_or_put_args);
    }

    return $this;
  }
  */
  /*
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
  */
  /*
  protected function _setupHeaders(Zend_Config $service) {
    if ($service->headers) {
      foreach ($service->headers as $header) {
        $this->addHeader($header);
      }
    }

    return $this;
  }
  */
}