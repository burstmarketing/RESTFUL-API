<?php

class Assembla_Model_Space extends Assembla_Model_Abstract {
  protected $_tickets;

  public function load( $element ){
    return parent::load( $element );
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