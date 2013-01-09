<?php

class Core_Config_JSON extends Core_Object implements Core_Config_Interface {

  public function load( $file ){
  if( ( $config = json_decode( file_get_contents($file), true ) ) !== null ){
    $this->setData($config);
    $this->_hasDataChanges = false;
  } else {
    throw new Exception('Could not load config located at: ' . $file);
  }
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

  public function getUris() {
  if( empty( $this->_data ) ){
    throw new Exception( 'No config has been loaded!' );
  }

  $_uri_array = array();
  foreach( $this->getServices() AS $_label => $_service ){
    if( array_key_exists( 'uri', $_service ) ){
    $_uri_array[] = $_service['uri'];
    } else {
    throw new Exception( 'No uri is defined for "' . $_label . '"' );
    }
  }
  return $_uri_array;
  }


  }


?>