<?php

class Core_API_Request_Json extends Core_API_Request {

  public function __construct(){
	parent::__construct();
  }

  protected function _validateCurlResponse( $str ){
    return ( @json_encode($str) !== false );
  }

  protected function _curlFailure( $ch, $out ){
    return $out;
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