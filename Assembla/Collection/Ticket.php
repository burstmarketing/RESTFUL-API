<?php
class Assembla_Collection_Ticket extends Assembla_Collection_Abstract {
  
  protected function _getModelClassName(){
    return "Assembla_Model_Ticket";
  }
  
  
  public function addStatusFilter( $status ){
    $this->_filters["statusFilter"] = array( "status" => $status );
    return $this;
  }
  

  private function _convertTicketStatus( $status_element ){
    if( (string) $status_element == "Array" ):
      return "0";
    else:
      return (string) $status_element;
    endif;
  }
  

  protected function statusFilter( $element, $status ){
    if( is_array($status) ){
      foreach( $status AS $s ){
	if( $this->_convertTicketStatus( $element->status ) == $s ){
	  return true;
	}
      }
      return false;
    } else {
      return $this->_convertTicketStatus( $element->status ) == $status;
    }
  }
  
  
  // Due Date filter                                                                                                                                                                     
  public function addHasDueDateFilter(){
    $this->_filters['hasDueDateFilter'] = array();
    return $this;
  }
  
  protected function hasDueDateFilter( $element ){    
    if( $element->xpath(".//custom-field[@name='Due Date']") ){
      return true;
    } else {
      return false;
    }
  }

  // Date Filter functions                                                                                                                                                               
  public function addDateAfterFilter( $date, $date_string ){
    $this->_filters["dateAfter"] = array( "date" => $date, "date_string" => $date_string );
    return $this;
  }
  
  
  protected function dateAfter( $element, $date, $date_string ){
    $element_date = new DateTime( (string) $element->$date_string );
    return (bool)( $element_date->getTimeStamp() > $date->getTimeStamp() );
  }
  
  public function addDateBeforeFilter( $date, $date_string ){
    $this->_filters["dateBefore"] = array( "date" => $date, "date_string" => $date_string );
    return $this;
  }
  
  protected function dateBefore( $element, $date, $date_string ){
    $element_date = new DateTime( (string) $element->$date_string );
    return (bool)( $element_date->getTimeStamp() < $date->getTimeStamp() );
  }

  public function addLastNumDaysFilter( $days, $date_string ){
    $this->_filters["lastNumDaysFilter"] = array( "days" => $days, "date_string" => $date_string );
    return $this;
  }
  
  protected function lastNumDaysFilter( $element, $days, $date_string ){
    $element_date = new DateTime( (string) $element->$date_string );
    $interval = $element_date->diff( new DateTime('') );
    
    return (bool)( $interval->days < $days );
    
  }
  




  }


?>