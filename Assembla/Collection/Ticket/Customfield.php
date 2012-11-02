<?php
class Assembla_Collection_Ticket_Customfield extends Assembla_Collection_Abstract {
  
  protected function _getModelClassName(){
        return "Assembla_Model_Ticket_Customfield";
  }

  public function addCustomField( $field ){
    if( $field->getId() ){
      if( $this->getField($field->getId()) ){
        return $this->updateField( $field->getId(), $field->getData() );
      } else {
        return $this->push($field);
      }
    } else {
      throw new Exception( 'Cannot add Custom Field without an ID!');
    }
  }

  public function updateField( $id, $data ){
    $field = $this->getField($id);
    foreach( $data AS $key => $value ){
      $field->setData( $key, $value );
    }
    return $this;
  }

  public function getField( $id ){
    foreach($this->getCollection() AS $field ){
      if($field->getId() == $id ){
        return $field;
      }
    }
    return false;
  }

  }

?>