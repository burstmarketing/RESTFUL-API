<?php

class Assembla_Model_Ticket_Customfield extends Assembla_Model_Abstract {

  static public function getTagName(){
        return "custom-field";
  }

  public function toXml($arrAttributes = array(), $addCdata=false){
    $xml = "<" . str_replace( " ", "_", $this->getName()) . ">";
    $xml .= $this->getValue();
    $xml .= "</" . str_replace( " ", "_", $this->getName()) . ">";
    return $xml;
  }

  public function load( $element ){
        $this->setData($element->asArray());
        return $this;
  }


  public function getName(){
    if( $attr = $this->getAttr() ){
      if( isset( $attr['name'] ) ){
        return $attr['name'];
      }
    }
    return false;
  }

  public function getId(){
    if( $attr = $this->getAttr() ){
      if( isset( $attr['id'] ) ){
        return $attr['id'];
      }
    }
    return false;
  }

  public function getType(){
    if( $attr = $this->getAttr() ){
      if( isset( $attr['type'] ) ){
        return $attr['type'];
      }
    }
    return false;
  }

  }


?>