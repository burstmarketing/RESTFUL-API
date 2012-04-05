<?php

class Assembla_Model_Ticket_Customfield extends Assembla_Model_Abstract {
  
  static public function getTagName(){
	return "custom-field";
  }

  public function toXml($arrAttributes = array(), $addCdata=false){
	$xml = "<" . $this->getData('attr/id') . ">";
	$xml .= $this->getValue();
	$xml .= "</" . $this->getData('attr/id') . ">";

	return $xml;
  }

  public function load( $element ){
	$this->setData($element->asArray());
	return $this;
  }
  
  }


?>