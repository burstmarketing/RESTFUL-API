<?php

abstract class Core_API_Request extends Core_Object {

  protected $_use_cache = false;
  public $outputHeaders = array();

  protected function _validateCurlResponse( $str ){
    return true;
  }

  abstract public function setAPI(Core_API &$api);

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

  public function headerParse($ch, $header) {
    $this->outputHeaders[] = $header;
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

  if( $this->getUsername() && $this->getPassword() ){
    curl_setopt($ch, CURLOPT_USERPWD, $this->getUsername() . ":" . $this->getPassword());
  }


  $headers = $this->getHeaders();
  if( is_array($headers) && ! empty($headers)){
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  }

  $out = curl_exec($ch);

  curl_setopt($ch, CURLOPT_HEADER, 1);
  curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'headerParse'));
  //Outputs response headers

  $outputWithHeaders = curl_exec($ch);
  $responseHeader = $this->outputHeaders[0];

  if(preg_match("/(204|404)/", $responseHeader)) {
    $out = json_encode(array("errors" => array($responseHeader)));
  }

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