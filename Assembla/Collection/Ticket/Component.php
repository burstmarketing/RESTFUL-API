<?php
class Assembla_Collection_Ticket_Component extends Assembla_Collection_Abstract {
  
  protected function _getModelElementTag(){
	return 'component';
  }

  protected function _getModelClassName(){
	return "Assembla_Model_Ticket_Component";
  }

  public function getComponentNameIDMap() {
    $_map = array();
    foreach( $this->getCollection() AS $component ){
      $_map[$component->getName()] = $component->getId();
    }
    return $_map;
  }

  public function getComponentIDNameMap() {
    $_map = array();
    foreach( $this->getCollection() AS $component ){
      $_map[$component->getId()] = $component->getName();
    }
    return $_map;
  }

  }


?>