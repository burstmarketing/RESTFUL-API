<?php

class Core_Config_JSON extends Core_Object implements Core_Config_Interface {

  public function load( $file ){
	$this->setData(json_decode( file_get_contents($file), true ));
	$this->_hasDataChanges = false;
  }
  
  public function getConfig( $uri ){
	return $this->getData( $uri );
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
	  $this->_data = $value;
	} else {
	  $this->_recursivelySetConfig( $uri, $value, $this->_data );
	  $this->_hasDataChanges = true;
	}

	return $this;
  }

  }


?>