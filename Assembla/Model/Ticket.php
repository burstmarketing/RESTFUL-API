<?php
  /*
   * <custom-fields>
   * <custom-field type="Date" name="Due Date" id="120993">2012/03/22</custom-field> 
   * </custom-fields>
   *
   */
class Assembla_Model_Ticket extends Assembla_Model_Abstract {

  public function getInvalidKeys() {
	return array("working_hour",
				 "assigned_to",
				 "reporter",
				 "status_name",
				 "documents", 
				 "id",
				 "tasks",
				 "ticket_comments",
				 "ticket_associations",
				 "from_support", 
				 "invested_hours",
				 "customfields",
				 "due_date"
				 );
  }
  

  static public function getTagName(){
	return "ticket";
  }


  public function load( $element ){
	parent::load( $element );

	// make sure we add custom fields to the data as if they were regular fields.
	if( $custom_fields =$element->xpath("./custom-fields/custom-field") ){
	  foreach( $custom_fields AS $field_element ){
		$field = $field_element->asArray();
		if( isset( $field['value'] ) && isset( $field['attr']['name'] ) ){
		  $key =  strtolower( preg_replace( '/-/', "_", $field['attr']['name'] ));
		  $key =  preg_replace( '/ /', "_", $key );
		  $this->setData( $key, $field['value'] );
		}
	  }
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