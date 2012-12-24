<?php

class Assembla_Model_Abstract extends Core_Model {

  static public function getTagName(){
        return "";
  }
  public function getInvalidKeys() {
        return array();
  }

  public function load( $element ){
        $this->setData($element);
        return $this;
  }

  //NOTE:  this should probably return recursive array keys
  protected function _getDataKeys(){
        return array_keys( $this->getData() );
  }

  public function toXml($arrAttributes = array(), $rootName = ''){

        if( !$rootName ){
          $rootName = $this::getTagName();
        }

        $arrAttributes = array_diff( $this->_getDataKeys(), $this->getInvalidKeys() );

        return parent::toXml( $arrAttributes, $rootName );
  }

  }

?>