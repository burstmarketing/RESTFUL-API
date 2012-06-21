<?php

abstract class Assembla_Collection_Abstract extends Core_Collection {

  protected $_filters = array();

  public function getFilters(){
    return $this->_filters;
  }

  protected function _setFilters( $filters ){
    $this->_filters = $filters;
    return $this;
  }

  protected function _getModelElementTag(){
    $model_name = $this->_getModelClassName();
	return $model_name::getTagName();
  }
  
  protected function _getModelClassName(){
	// This is almost certainly NOT what we want
	// please override this function in inherited
	// classes
	return "Core_Model";
  }

  public function load( $element ){
	$classname = $this->_getModelClassName();
	$_elements = $element->xpath( $this->_getModelElementTag() );
	foreach( $_elements AS $_element ){
	  
	  $sentinal = true;
	  foreach( $this->_filters AS $_func_name => $_args ){
	    array_unshift($_args, $_element);
	    if( ! $sentinal = call_user_func_array( array( $this, $_func_name), $args ) ){
	      break;
	    }
	  }

	  if( $sentinal ){
	    $model = new $classname();
	    $model->load( $_element );	    
	    $this->offsetSet( "", $model );
	  }

	}
	
	return $this;
  }



}


?>