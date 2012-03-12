<?php


class Assembla_API extends Core_API {


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
	  return "http://" . $this->getConfig('credentials/username') .
		":"       . $this->getConfig('credentials/password') .
		"@"       . $this->getConfig('defaults/url') .
		"/";
	  
  }

  protected function _preProcessRequest( Core_API_Request $request ){
	return $request;
  }

  protected function _postProcessRequest( Core_API_Request $request ){
	$request->setUrl( $this->_getAPIUrl() );
	return $request;
  }
  

  }


?>