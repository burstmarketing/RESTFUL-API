<?php

abstract class Core_API_Request extends Zend\Http\Request {

  protected $_use_cache = false;

  protected function _validateCurlResponse( $str ){
    return true;
  }

  abstract public function setAPI(Core_API &$api);
  abstract public function getResponseClassName();


  protected function _curlFailure( $ch, $out ){
    throw new Exception( 'curl_exec failed with: ' . curl_error( $ch ) );
  }

  public function useCache( $use_cache = null ){
    if( $use_cache === null ){
      return $this->_use_cache;
    }
    $this->_use_cache = $use_cache;
    return $this;
  }
  abstract protected function _setCache( $key, $value );
  abstract protected function _getCache( $key );
  abstract protected function _getCacheKey();

}
?>