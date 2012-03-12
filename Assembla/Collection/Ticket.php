<?php
class Assembla_Collection_Ticket extends Assembla_Collection_Abstract {
  
  protected function _getModelElementTag(){
	return 'ticket';
  }

  protected function _getModelClassName(){
	return "Assembla_Model_Ticket";
  }


  }


?>