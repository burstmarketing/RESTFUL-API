<?php

class Assembla_Collection_Task extends Assembla_Collection_Abstract {

  protected function _getModelElementTag(){
	return 'task';
  }

  protected function _getModelClassName(){
	return "Assembla_Model_Task";
  }


  } 
?>