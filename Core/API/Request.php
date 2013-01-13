<?php

abstract class Core_API_Request extends Zend\Http\Request {

  protected $_use_cache = false;

  public function useCache( $use_cache = null ){
    if( $use_cache === null ){
      return $this->_use_cache;
    }
    $this->_use_cache = $use_cache;
    return $this;
  }

  abstract public function manageRequestData( $data );

  abstract protected function _setCache( $key, $value );
  abstract protected function _getCache( $key );
  abstract protected function _getCacheKey();

}
?>