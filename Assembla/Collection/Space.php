<?php
class Assembla_Collection_Space extends Assembla_Collection_Abstract {

  protected function _getModelElementTag(){
    return "space";
  }

  protected function _getModelClassName(){
    return "Assembla_Model_Space";
  }

  public function loadSpacesByTicketCollection( $tickets, $use_cache = false ){
    if( $tickets instanceof Assembla_Collection_Ticket ){
      $model_name = $this->_getModelClassName();
      $space_ids = $tickets->asArraySortedBySpaceId();

      // by checking against current space ids we can load a space collection
      // against multiple ticket collections
      $current_space_ids = $this->getCollectionIds();

      foreach( $space_ids AS $space_id => $ticket_collection ){
        if( !in_array( $space_id, $current_space_ids ) ){
          $model = new $model_name;
          $model->loadById($space_id, $use_cache);
          $model->setTickets( $ticket_collection );
          $this->offsetSet("", $model );
        }
      }

    }
    return $this;
  }

  public function getCollectionIds() {
    $collection_ids = array_map( function($e) { return $e->getId(); }, $this->getCollection() );
    return ($collection_ids) ? $collection_ids : array();
  }

  public function loadByIds( $space_ids, $use_cache = false ){
    if( !is_array( $space_ids ) ){ $space_ids = array( $space_ids ); }

    $model_name = $this->_getModelClassName();

    foreach( $space_ids AS $space_id ){
      if( is_string($space_id) ){
        $model = new $model_name();
        $this->offsetSet( "", $model->loadById($space_id, $use_cache) );
      }
    }

    return $this;

  }



}


?>