<?php

abstract class Assembla_Collection_Abstract extends Core_Collection {

  abstract protected function _getModelElementTag();

  protected function _getModelClassName(){
	// This is almost certainly NOT what we want
	// please override this function in inherited
	// classes
	return "Core_Model";
  }

  public function load( $element ){
	$classname = $this->_getModelClassName();
	$_milestones = $element->xpath( $this->_getModelElementTag() );
	foreach( $_milestones AS $_milestone_element ){
	  $model = new $classname();
	  $model->load( $_milestone_element );
	  $this->offsetSet( "", $model );
	}
	
	return $this;
  }



}


?>