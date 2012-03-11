<?php

abstract class Core_API_Request extends Core_Object {

  public function send( array $args = array() ){
	
	if( $this->_useCache() && $this->_getCache($this->_getCacheKey()) ) {
	  return $this->_getCache( $this->_getCacheKey() );
	} else {
	  if( $this->getUrl() != '' ) {
		$ch = curl_init( $this->getUrl() );
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		if( is_array($this->getHeaders()) && ! empty( $this->getHeaders() )):
		  curl_setopt($ch, CURLOPT_HTTPHEADER, array_values($this->getHeaders()));
		endif;

		$out = curl_exec ($ch);
		
		if( $out !== false ){
		  $this->_setCache( $this->_getCacheKey( $ch ), $out );
		  curl_close($ch);	  
		} else {
		  $out = $this->_curlError($ch);
		}                  
		return $out;	  
		
	  } else {
		throw new Exception( 'No url was set on the Request Object!' ); 
	  }
	}
  }
  
  protected function _curlFailure( $ch ){
	throw new Exception( 'curl_exec failed with: ' . curl_error( $ch ) ); 
  }
  
  abstract protected function _useCache();
  abstract protected function _setCache( $key, $value );
  abstract protected function _getCache( $key );
  abstract protected function _getCacheKey();

?>