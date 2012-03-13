<?php

interface Core_Config_Interface {
  
  // called to load the config, impelementing
  // class should probably save the contents of the
  // file in some kind of protected variable
  public function load( $file );

  // Returns an element of the config,
  // uri will be passed in as 'foo/bar/baz'
  // for nested elements of the config. special
  // uri '/' should return the entire config.
  public function getConfig( $uri );

  // sets a config element based on the uri
  // passed in. This uri should then be available
  // when called from getConfig (even if it is nested)
  public function setConfig( $uri, $value );

  // return an array of all the service uri's as matchable
  // regurlar expressions.
  public function getUris();

  }
