<?php

abstract class Core_API_Request extends Core_Object {  

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


  public function send( ){
	
	if( $this->_useCache() && $this->_getCache($this->_getCacheKey()) ) {
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
		curl_setopt($ch,CURLOPT_USERPWD, $this->getUsername() . ":" . $this->getPassword()); 


		$headers = $this->getHeaders();
		if( is_array($headers) && ! empty($headers)){
		  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}


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

  }
?>