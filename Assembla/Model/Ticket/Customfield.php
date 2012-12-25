<?php

class Assembla_Model_Ticket_Customfield extends Assembla_Model_Abstract {

  public function load(array $element) {
    return $this->setData($element);
  }

  public function getName() {
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