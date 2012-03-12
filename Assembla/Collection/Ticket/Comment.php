<?php
class Assembla_Collection_Ticket_Comment extends Assembla_Collection_Abstract {
  
  protected function _getModelElementTag(){
	return 'comment';
  }

  protected function _getModelClassName(){
	return "Assembla_Model_Ticket_Comment";
  }


  }


?>