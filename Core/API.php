<?php


class Core_API {
  protected $_config;
  protected $_config_base_type;

  public function loadConfig( $file ){
	if(is_readable(  RESTFUL_API_LOADER::getBaseDir() . $file ) ){
	  // skeleton code to add other config types
	  // currently just defaults to json.
	  switch( $this->_config_base_type ){
	  default:
		$this->_config = new Core_Config_JSON;
	  }

	  $this->_config->load( RESTFUL_API_LOADER::getBaseDir() . $file );
	} else {
	  throw new Exception( "Could not load file " . RESTFUL_API_LOADER::getBaseDir() . $file);
	}
  }

  public function getConfig( $uri ){
	return $this->_config->getConfig( $uri );
  }

  public function setConfig( $uri, $value ){
	$this->_config->setConfig($uri, $value );
	return $this;
  }

  }