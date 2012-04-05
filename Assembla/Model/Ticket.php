<?php
  /*
   * <custom-fields>
   * <custom-field type="Date" name="Due Date" id="120993">2012/03/22</custom-field> 
   * </custom-fields>
   *
   */
class Assembla_Model_Ticket extends Assembla_Model_Abstract {

  public function getInvalidKeys() {
	return array("working-hour",
				 "assigned-to",
				 "reporter",
				 "status-name",
				 "documents", 
				 "id",
				 "tasks",
				 "ticket-comments",
				 "ticket-associations",
				 "from-support", 
				 "invested-hours",
				 "customfields",
				 "due-date"
				 );
  }
  

  static public function getTagName(){
	return "ticket";
  }

  // UGLY!!!

  public function setData($key, $value=null){
	if( is_array( $key ) ){

	  $customfields = ( isset($key['custom_fields']) ) ? 'custom_fields' : 'custom-fields';

	  if( isset( $key[$customfields] ) && is_array($key[$customfields]) ){
		$_custom_fields_collection = new Assembla_Collection_Ticket_Customfield();
		foreach ($key[$customfields] AS $_custom_field_value){
		  $_custom_field = new Assembla_Model_Ticket_Customfield();
		  $_custom_field->setData($_custom_field_value);
		  $_custom_fields_collection[] = $_custom_field;
		}
		$key[$customfields] = $_custom_fields_collection;
		parent::setData($key);
	  }
	} else if( ( $key == 'custom_fields' || $key == 'custom-fields' ) && is_array($value) ){

	  $_custom_fields_collection = new Assembla_Collection_Ticket_Customfield();
	  foreach ($value AS $_custom_field_value){
		$_custom_field = new Assembla_Model_Ticket_Customfield();
		$_custom_field->setData($_custom_field_value);
		$_custom_fields_collection[] = $_custom_field;
	  }
	  parent::setData($key, $_custom_fields_collection);
	} else {
	  parent::setData($key, $value);
	}
  }

  public function load( $element ){
	parent::load( $element );

	if( $custom_fields_element = $element->{'custom-fields'} ){
	  $custom_fields_collection = new Assembla_Collection_Ticket_Customfield();
	  $custom_fields_collection->load( $custom_fields_element );
	  $this->setData('custom_fields', $custom_fields_collection);
	}

	return $this;
  }
  
  public function isOnTime(){
	// to be implemented
  }

  public function getMilestone($api){
	// to be implemented
  }

  public function getMilestoneTitle(){
	// to be implemented
  }

  }


?>