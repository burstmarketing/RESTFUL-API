<?php


class Assembla_API extends Core_API {
  protected $_request_class = "Assembla_API_Request";
  protected $_response_class = "Assembla_API_Response";


  // a little sneaky if '$file' is already a config object
  // set _config to that,  else load $file as if it were a file
  public function loadConfig( $file ){
	if( is_object($file) && $file instanceof Zend_Config ){
	  $this->_config = $file;
	} else {
	  parent::loadConfig( $file );
	}

	return $this;
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
  
  public function getApiKeySecrete(){
    return $this->getConfig('credentials/api_key_secret');
  }

  public function setApiKey($key){
    $this->setConfig('credentials/api_key', $key);
    return $this;
  }

  public function setApiKeySecrete($secret){
    $this->setConfig('credentials/api_key_secret', $secret);
    return $this;
  }



  protected function _getRequest(){
    $request = new $this->_request_class;
    $request->useCache( $this->useCache() );
    return $request;
  }

  protected function _getResponse(){
    $response = new $this->_response_class;
    return $response;
  }

  }


?>