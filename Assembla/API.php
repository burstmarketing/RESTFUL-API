<?php


class Assembla_API extends Core_API {


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


  protected function _getRequest(){
	return new Assembla_API_Request;
  }

  protected function _getResponse(){
	return new Assembla_API_Response;
  }

  protected function _getAPIUrl(){

	if( ! $this->getConfig('credentials/username') ||
		! $this->getConfig('credentials/password') ) {
	  throw new Exception('Credentials are not set on API!');
	}
	if(	! $this->getConfig('defaults/url') ){
	  throw new Exception('Default URL is not set!');
	}
	// http://user:password@www.assembla.com/
	  return "http://" . $this->getConfig('defaults/url') . "/";
	  
  }

  protected function _preProcessRequest( Core_API_Request $request ){
	return $request;
  }

  protected function _postProcessRequest( Core_API_Request $request ){
	$request->setUsername( $this->getConfig('credentials/username') );
	$request->setPassword( $this->getConfig('credentials/password') );
	$request->setUrl( $this->_getAPIUrl() );

	$service = $this->getService( $request->getKey() );
	if( isset( $service['headers'] ) ){
	  foreach( $service['headers'] AS $header ){
		$request->addHeader( $header );
	  }
	}

	return $request;
  }
  

  }


?>