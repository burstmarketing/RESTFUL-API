<?php

class Core_API_Request_XML extends Core_API_Request {

  public function __construct(){
	parent::__construct();
	$this->addHeader( "Accept: application/xml" );
  }

  protected function _validateCurlResponse( $str ){
    return ( @simplexml_load_string($str) !== false );
  }

  protected function _curlFailure( $ch, $out ){
    $ret = '<?xml version="1.0" encoding="UTF-8"?><errors type="array">';

    if( $err = curl_error( $ch ) ){
      $ret .= '<error>'. $err .'</error>';
    }

    if( $out == '' ){
      $ret .= '<error>Service returned no response!</error>';
    } else {
      $ret .= '<error>' . $out . '</error>';
    }
    $ret .= '</errors>';
    return $ret;
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