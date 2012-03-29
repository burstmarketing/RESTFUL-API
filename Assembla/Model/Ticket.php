<?php

  /*
   Invalid - 
   working_hour, 
   assigned_to, 
   reporter, 
   status_name, 
   documents, 
   id, 
   tasks, 
   ticket_comments, 
   ticket_associations, 
   from_support, 
   invested_hours

   Valid -
   assigned_to_id
   completed_date
   component_id
   created_on
   description
   from_support
   id
   importance
   is_story
   milestone_id
   notification_list
   number
   priority
   reporter_id
   space_id
   status
   status_name
   story_importance
   summary
   updated_at
   working_hours
   working_hour
   estimate
   total_estimate
   invested_hours
   assigned_to
   reporter
   documents
   ticket_comments
   tasks
   ticket_associations
   
   */



class Assembla_Model_Ticket extends Assembla_Model_Abstract {
  

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