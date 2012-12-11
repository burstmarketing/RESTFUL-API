<?php
class Assembla_Collection_Ticket extends Assembla_Collection_Abstract {
  

  // cache spaces loaded by the component filter so we don't have
  // to load the space and its components more than once.
  protected $_component_filter_cache = array();

  protected function _getModelClassName(){
    return "Assembla_Model_Ticket";
  }   

  public function getSpaceTickets( $space_id ){
    return new Blast_Assembla_Collection_Ticket( array_filter( $this->getCollection(),
							       function( $ticket ) use ($space_id) { 
								return $ticket->getSpaceId() == $space_id;
							      } ) );
  }

  public function getUniqueSpaceIdsFromTickets(){
    return array_unique( array_map( function( $ticket ){ return $ticket->getSpaceId(); }, $this->getCollection() ) );
  }
  

  public function asArraySortedBySpaceId(){
    $space_tickets = array();
    $class_name = get_class($this);

    // chunk out the tickets for each space                                                                     
    foreach( $this->getCollection() AS $ticket ){
      if(! array_key_exists( $ticket->getSpaceId(), $space_tickets ) ){
        $space_tickets[$ticket->getSpaceId()] = new $class_name;
      }
      $space_tickets[$ticket->getSpaceId()]->push( $ticket );
    }
    return $space_tickets;
  }


  protected function _appendPagedReport( $id, $use_cache ){
    $i = 0; 
    do{ 
      $i++; 
      $length = count($this);

      $request = $this->_getAPI($use_cache)->generateRequest("paged_portfolio_ticket_report", array( $id, $i ) );
      $http_response = $request->send();

      $element = new Assembla_API_XML_Element( $http_response );
      if( ! count($element->xpath('error')) ) {
	$element = new Assembla_API_XML_Element( $http_response );
	$this->load($element);
      } else {
	$errors = $element->asCanonicalArray();
	$err = "Errors were encountered:";
	foreach( $errors as $e ){
	  if( $e == "HTTP Basic: Access denied." ){
	    throw new Core_Exception_Auth( "Authentical credentials failed!" );
	  } else {
	    $err .= " " . $e . " ";
	  }
	}
	throw new Exception( $err );
	
      }     
      // asCanonicalArray will return "" if empty - this means 
      // we're out of tickets so break the loop.  
    } while( (bool) $element->asCanonicalArray() );
  }




  /*
   *    Filter related functions
   */


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
    if( !is_array($status) ){ 
      $status = array($status); 
    }
    
    foreach( $status AS $s ){
      if( $this->_convertTicketStatus( $element->status ) == $s ){
	return true;
      }
    }

    return false;
  }

  public function setComponentNameOnTickets(){

    foreach($this->getCollection() AS $ticket ){      
      $space_id = $ticket->getSpaceId();
      if( !in_array($space_id, array_keys( $this->_component_filter_cache ) ) ){
	$space = new Blast_Assembla_Model_Space;                                                                                              
	$space->loadById($space_id, true)->loadComponents(true);    
	$this->_component_filter_cache[$space_id] = $space;
      } else {
	$space = $this->_component_filter_cache[$space_id];
      }
      
      $component_map = $space->getComponents()->getComponentIDNameMap();
      if($ticket->getComponentId() != '' && isset($component_map[$ticket->getComponentId()])){
	$ticket->setComponent($component_map[$ticket->getComponentId()]);
      } else {
	$ticket->setComponent('');
      }
    }

  }

  protected function excludeComponentFilter( $element, $components ){
    if( !is_array($components) ){
      $components = array($components);
    }    

    $space_id = (string) $element->{'space-id'};

    if( !in_array($space_id, array_keys( $this->_component_filter_cache ) ) ){
      $space = new Blast_Assembla_Model_Space;                                                                                              
      $space->loadById($space_id, true)->loadComponents(true);    
      $this->_component_filter_cache[$space_id] = $space;
    } else {
      $space = $this->_component_filter_cache[$space_id];
    }

    $component_map = $space->getComponents()->getComponentIDNameMap();
    $ticket_component = (string) $element->{'component-id'};

    if( isset($component_map[$ticket_component]) ){
      return !in_array( $component_map[$ticket_component], $components);
    }
    
    return true;

  }

  public function addExcludeComponentFilter( $components ){
    $this->_filters["excludeComponentFilter"] = array( "components" => $components );
    return $this;
  }


  protected function excludeStatusFilter( $element, $status ){
    if( !is_array($status) ){ 
      $status = array($status); 
    }
    
    if( !in_array( $this->_convertTicketStatus( $element->status ), $status ) ){
      return true;
    }

    return false;
  }
  

  public function addExcludeStatusFilter( $status ){
    $this->_filters["excludeStatusFilter"] = array( "status" => $status );
    return $this;
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
  
  public function addNotTodayFilter( $date_string ){
    $this->_filters["notToday"] = array( 'date_string' => $date_string,
					 'today' => date('Ymd', time()) );
    return $this;
  }

  // @td   Edge case where this runs at midnight and some tickets get through
  //       and others don't because all of a sudden the date changes
  protected function notToday($element, $date_string, $today){
    $d = new Datetime( (string) $element->$date_string );
    return $d->format('Ymd') != $today;
  }

  }


?>