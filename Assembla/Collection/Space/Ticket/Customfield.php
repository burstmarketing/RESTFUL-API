<?php
class Assembla_Collection_Space_Ticket_Customfield extends Assembla_Collection_Abstract {

  protected function _getModelElementTag(){
    return 'CustomField';
  }

  protected function _getModelClassName(){
    return "Assembla_Model_Space_Ticket_Customfield";
  }


}
?>