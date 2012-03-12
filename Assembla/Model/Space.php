<?php

class Assembla_Model_Space extends Assembla_Model_Abstract {
  protected $_tickets;

  public function load( $element ){
	parent::load( $element );

	// sort of a hack,  all the spaces tools stuff is based on attributes
	// so default canonical array stuff is NOT what we want.

	$spaces_tools = $element->{"spaces-tools"}->asArray();

	// spaces-tools element is of type='array'  so our keys
	// are attr,0,1,2.. after asArray() function. This is not so
	// helpful.  Loop through spaces_tools getting the name of the
	// tool and set the key.. this should make things easier later.

	$xml = $element->asXml();

	foreach( $spaces_tools AS $key => $tool ){
	  if( $key !== "attr" ){
		$spaces_tools[$tool['attr']['type']] = $tool;
		unset($spaces_tools[$key]);
	  }
	}

	$this->setSpacesTools(  $spaces_tools );


	return $this;
  }

  public function getTicketCollection(Assembla_API $api){
	if( !$this->_tickets ){
	  // use the API to load the tickets
	}
	return $this->_tickets;
  }

  protected function _getSubversionTool( ){
	return $this->getData('spaces_tools/SubversionTool');
  }

  public function getSubversionUrl( ) {
	$tool = $this->_getSubversionTool();
	if( isset( $tool['url']['value'] ) ){
	  return $tool['url']['value'];
	}
	return false;
  }


  public function getOnTimeTickets(){
	//to be implemented
	return 0;
  }

  public function getOnTimePercent(){
	if( count($this->_tickets) ):
	  return sprintf( "%.2f", (float) $this->getOnTimeTickets()/count($this->_tickets) * 100);
	else:
	  return sprintf( "%.2f", (float) 0.00 );
	endif;
  }      

  
  }


?>