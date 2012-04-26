<?php
class Assembla_Collection_User extends Assembla_Collection_Abstract {
  
  protected function _getModelElementTag(){
	return 'user';
  }

  protected function _getModelClassName(){
	return "Assembla_Model_User";
  }


  }


?>