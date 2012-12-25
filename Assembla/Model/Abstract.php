<?php

class Assembla_Model_Abstract extends Core_Model {

  public function load( $element ){
    return $this->setData($element);
  }

  public function getInvalidKeys() {
    return array();
  }

  //NOTE:  this should probably return recursive array keys
  protected function _getDataKeys(){
    return array_keys( $this->getData() );
  }
}