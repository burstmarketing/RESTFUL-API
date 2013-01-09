<?php
class Assembla_Collection_Milestone extends Assembla_Collection_Abstract {

  protected function _getModelElementTag(){
    return 'milestone';
  }

  protected function _getModelClassName(){
    return "Assembla_Model_Milestone";
  }


}


?>