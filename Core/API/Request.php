<?php

abstract class Core_API_Request extends Core_Object {  

  protected $_use_cache = false;

  protected function _validateCurlResponse( $str ){
    return true;
  }

  public function addHeader( $header ){
	if( $headers = $this->getHeaders() ){
	  if( is_array( $headers ) ){
		if( !in_array( $header, $headers ) ){
		  $headers[] = $header;
		  $this->setHeaders( $headers );
		}
	  }
	} else {
	  $this->setHeaders( array( $header ) );
	}
  }


  public function send() {	
    if( $this->useCache() && $this->_getCache($this->_getCacheKey()) ) {
      return $this->_getCache( $this->_getCacheKey() );
    } else {
      if( $this->getUrl() != '' && $this->getUri() != '' ) {

	$ch = curl_init( $this->getUrl() . $this->getUri() );

	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->getType() );
		
	if( (bool) $this->getCurlData() ){
	  curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getCurlData());
	}

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1 );
	curl_setopt($ch, CURLOPT_USERPWD, $this->getUsername() . ":" . $this->getPassword()); 


	$headers = $this->getHeaders();
	if( is_array($headers) && ! empty($headers)){
	  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	}


	$out = curl_exec ($ch);
		
	if( $out !== false && $this->_validateCurlResponse($out) ){
	  if( $this->useCache() ){
	    $this->_setCache( $this->_getCacheKey(), $out );
	  }
	  curl_close($ch);	  
	} else {
	  $out = $this->_curlFailure($ch, $out);
	}                  
	return $out;	  
		
      } else {
	throw new Exception( 'No url was set on the Request Object!' ); 
      }
    }
  }
  
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