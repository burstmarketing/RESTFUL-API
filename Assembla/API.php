<?php

class Assembla_API extends Core_API {

  // a little sneaky if '$file' is already a config object
  // set _config to that,  else load $file as if it were a file
  public function loadConfig( $file ){
    if( is_object($file) && $file instanceof Zend\Config\Config ){
      $this->_config = $file;
    } else {
      parent::loadConfig( $file );
    }

    return $this;
  }

  public function getClient( $args = false){
    $client = new Assembla_API_Client($args);
    $client->setOptions( array("sslverifypeer" => false) );
    return $client;
  }


  public function getUserName(){
    return $this->getConfig('credentials/username');
  }

  public function getPassword(){
    return $this->getConfig('credentials/password');
  }

  public function setUserName( $username ){
    $this->setConfig('credentials/username', $username );
    return $this;
  }

  public function setPassword( $password ){
    $this->setConfig('credentials/password', $password );
    return $this;
  }

  public function getApiKey(){
    return $this->getConfig('credentials/api_key');
  }

  public function getApiKeySecret(){
    return $this->getConfig('credentials/api_key_secret');
  }

  public function setApiKey($key){
    $this->setConfig('credentials/api_key', $key);
    return $this;
  }

  public function setApiKeySecret($secret){
    $this->setConfig('credentials/api_key_secret', $secret);
    return $this;
  }

  protected function _getRequest(){
    $request = new $this->_request_class;
    $request->useCache( $this->useCache() );
    return $request;
  }

  protected function _getResponse(){
    return new $this->_response_class;
  }
}