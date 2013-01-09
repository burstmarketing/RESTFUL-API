<?php
class Assembla_Collection_Ticket_Component extends Assembla_Collection_Abstract {

  protected function _getModelElementTag(){
    return 'component';
  }

  protected function _getModelClassName(){
    return "Assembla_Model_Ticket_Component";
  }


}


?>