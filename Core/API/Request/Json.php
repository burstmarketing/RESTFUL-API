<?php

class Core_API_Request_Json extends Core_API_Request {

  public function setAPI(Core_API &$api) {}

  public function getResponseClassName(){
    return "Core_API_Response_Json";
  }

  protected function _validateCurlResponse( $str ){
    return ( @json_decode($str) !== false );
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