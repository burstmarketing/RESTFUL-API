<?php
class Assembla_Collection_Ticket_Statuses extends Assembla_Collection_Abstract {
  
  protected function _getModelElementTag(){
	return 'ticket-status';
  }

  protected function _getModelClassName(){
	return "Assembla_Model_Ticket_Statuses";
  }


  }


?>