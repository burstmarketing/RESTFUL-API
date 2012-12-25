<?php
/*
 * <custom-fields>
 * <custom-field type="Date" name="Due Date" id="120993">2012/03/22</custom-field>
 * </custom-fields>
 *
 */
class Assembla_Model_Ticket extends Assembla_Model_Abstract {

  const TICKET_STATUS_NEW      = "0";
  const TICKET_STATUS_ACCEPTED = "1";
  const TICKET_STATUS_INVALID  = "2";
  const TICKET_STATUS_FIXED    = "3";
  const TICKET_STATUS_TEST     = "4";

  const TICKET_UPDATED_AT     = "updated-at";
  const TICKET_CREATED_AT     = "created-at";
  const TICKET_COMPLETED_DATE = "completed-date";


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
                 "due-date");
  }

  protected function _getCustomfieldModel(){
    return new Assembla_Model_Ticket_Customfield();
  }

  protected function _getCustomfieldCollection(){
    return new Assembla_Collection_Ticket_Customfield();
  }

  // UGLY!!!
  public function setData($key, $value=null){

    if( is_array( $key ) ){
      $customfields = ( isset($key['custom_fields']) ) ? 'custom_fields' : 'custom-fields';

      if( isset( $key[$customfields] ) && is_array($key[$customfields]) ){
        $_custom_fields_collection = $this->_getCustomfieldCollection();
        foreach ($key[$customfields] AS $_custom_field_value){
          $_custom_field = $this->_getCustomfieldModel();
          $_custom_field->setData($_custom_field_value);
          $_custom_fields_collection[] = $_custom_field;
        }
        $key[$customfields] = $_custom_fields_collection;

      }

      parent::setData($key);

    } else if( ( $key == 'custom_fields' || $key == 'custom-fields' ) && is_array($value) ){

      $_custom_fields_collection = $this->_getCustomfieldCollection();
      foreach ($value AS $_custom_field_value){
        $_custom_field = $this->_getCustomfieldModel();
        $_custom_field->setData($_custom_field_value);
        $_custom_fields_collection[] = $_custom_field;
      }
      parent::setData($key, $_custom_fields_collection);
    } else {
      parent::setData($key, $value);
    }
  }

  public function addCustomField( $data ){
    if( !$this->hasCustomFields() ){
      $this->setCustomFields( $this->_getCustomfieldCollection() );
    }
    $_custom_field = $this->_getCustomfieldModel();
    $_custom_field->setData( $data );

    $this->getCustomFields()->addCustomField( $_custom_field );
    return $this;

  }

  public function load($element) {
    parent::load($element);

    if (isset($element['custom_fields'])) {
      $custom_fields_collection = $this->_getCustomfieldCollection();
      $custom_fields_collection->load((array) $element['custom_fields']);

      $this->setData('custom_fields', $custom_fields_collection);
    }

    return $this;
  }

  public function getEstimate(){
    if ($this->hasEstimate()) {
      switch ($this->getData('estimate')) {
      case 'Small':
        return 4.0;
        break;
      case 'Medium':
        return 8.0;
        break;
      case 'Big':
        return 16.0;
        break;
      case 'None':
        return 0.0;
        break;
      default:
        return (float) $this->getData('estimate');
      }
    }

    return false;
  }

  public function getMilestoneTitle() {
    $milestone = $this->getMilestone();

    if ($milestone instanceof Assembla_Model_Milestone) {
      return $milestone->getTitle();
    } elseif ($this->getMilestoneId()) {
      return $this->getMilestoneId();
    } else {
      return 'No Milestone';
    }
  }

  public function getMilestone() {
    return false;
  }

  public function formatDate($date_str) {
    try {
      $date = new DateTime($date_str);
      return $date->format('m/d/Y');
    } catch (Exception $e) {
      return $date_str;
    }
  }

  public function isOnTime() {
    if ((bool) $this->getDueDate() && (bool) $this->getCompletedDate()) {
      $due = strtotime( 'today', strtotime($this->getDueDate()) );
      $completed = strtotime( 'today', strtotime($this->getCompletedDate()) );
      return (bool) ($completed <= $due);
    }

    return false;
  }
}