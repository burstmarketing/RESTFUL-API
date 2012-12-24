<?php
class Assembla_Collection_Ticket extends Assembla_Collection_Abstract {

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

      if( simplexml_load_string($http_response) ){
        $element = new Assembla_API_XML_Element( $http_response );
        $this->load($element);
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
      if ($element['status'] == $s) {
        return true;
      }
    }

    return false;
  }

  protected function excludeStatusFilter( $element, $status ){
    $status = (array) $status;

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





  }


?>