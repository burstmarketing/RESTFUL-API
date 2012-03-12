<?php

class Core_API_Request_XML extends Core_API_Request {

  public function __construct(){
	parent::__construct();
	$this->addHeader( "Accept: application/xml" );
  }

  protected function _curlFailure( $ch ){
	return '<?xml version="1.0" encoding="UTF-8"?>';
  }
  
  protected function _useCache(){
	return false;
  }
  protected function _setCache( $key, $value ){
	return;
  }
  protected function _getCache( $key ){
	return;
  }
  protected function _getCacheKey(){
	return '';
  }
  }
?>