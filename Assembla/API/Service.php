<?php

class Assembla_API_Service extends Core_API_Service {
  protected $_api;


  public function __construct() {
    $args = func_get_args();

    if (empty($args[0]) || !( $args[0] instanceof Core_API )) {
      throw Assembla_Exception( __CLASS__ . " must be instantiated with an object of type Core_API!");
    }

    $this->_api = $args[0];
    $this->_data = array();

  }

  public function __call($method, $args) {
    switch (substr($method, 0, 3)) {
    case 'get' :
      $key = $this->_underscore(substr($method,3));

      // if we can't get it off the actual service _data[]  then see
      // if its in the API's default section. This couples Service
      // objects to their API object,  but i don't think that will prove
      // to be an issue - Chris
      if( ($data = parent::__call($method, $args )) === null ){
        $data = $this->getAPI()->getConfig('defaults/' . $key);
      }
      return $data;

    default :
      return parent::__call($method, $args);
    }
  }



  public function getAPI(){
    return $this->_api;
  }


}