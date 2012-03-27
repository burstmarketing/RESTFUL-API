<?php
class Assembla_Collection_Ticket extends Assembla_Collection_Abstract {
  
  protected function _getModelElementTag(){
    $model_name = $this->_getModelClassName();
	return $model_name::getTagName();
  }

  protected function _getModelClassName(){
	return "Assembla_Model_Ticket";
  }


  }


?>