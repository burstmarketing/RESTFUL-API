<?php

class Assembla_Model_Abstract extends Core_Model {

  static public function getTagName(){
	return "";
  }

  public function load( $element ){
	$this->setData( $element->asCanonicalArray() );
	return $this;
  }

  public function toXml(array $arrAttributes = array(), $rootName = 'item', $addOpenTag=false, $addCdata=true){
	if( $rootName == 'item' ){
	  $rootName = $this::getTagName();
	}
	return parent::toXml( $arrAttributes, $rootName, $addOpenTag, $addCdata );
  }

  }

?>