<?php

class Assembla_Model_Abstract extends Core_Model {

  public function load( $element ){
	$this->setData( $element->asCanonicalArray() );
	return $this;
  }

  }

?>