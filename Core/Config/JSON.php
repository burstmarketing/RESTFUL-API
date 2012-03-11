<?php

class Core_Config_JSON implements Core_Config_Interface {

  protected $_config;

  public function load( $file ){
	$this->_config =  json_decode( file_get_contents($file), true );
  }

  protected function _recursivelyGetConfig( $uri, $config ){
	$uri_parts = explode("/", $uri );
	if( $uri == "" ){
	  return $config;
	} else {
	  $_key = array_shift( $uri_parts );
	  if( is_array($config) && array_key_exists( $_key, $config ) ){
		return $this->_recursivelyGetConfig( implode( "/", $uri_parts), $config[$_key]);
	  } else {
		throw new Exception( $_key );
	  }
	}

  }

  public function getConfig( $uri ){
	if( $uri == "/" ){
	  return $this->_config;
	} else {
	  try {
		return $this->_recursivelyGetConfig( $uri, $this->_config );
	  } catch (Exception  $e ){
		throw new Exception( "The key " . $e->getMessage() . " does not exist in config uri " . $uri );
	  }
	}

	return $this;
  }


  protected function _recursivelySetConfig( $uri, $value, &$config ){
	$uri_parts = explode("/", $uri );
	if( count($uri_parts) === 1 ){
	  $config[$uri_parts[0]] = $value;
	} else {

	  $_key = array_shift($uri_parts);

	  if( !is_array($config) || !array_key_exists( $_key, $config ) ){
		$config[$_key] = array();
	  }
	  
	  $this->_recursivelySetConfig( implode("/", $uri_parts), $value, $config[$_key] );

	  }
	}
  

  public function setConfig( $uri, $value ){
	if( $uri == "/" ){
	  $this->_config = $value;
	} else {
	  $foo = $this->_recursivelySetConfig( $uri, $value, $this->_config );
	}

	return $this;
  }

  }


?>