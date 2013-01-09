<?php
class Assembla_Collection_Ticket_Association extends Assembla_Collection_Abstract {

  protected function _getModelElementTag(){
    return 'ticket-association';
  }

  protected function _getModelClassName(){
    return "Assembla_Model_Ticket_Association";
  }


}


?>