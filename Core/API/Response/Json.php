<?php

class Core_API_Response_Json extends Core_API_Response {

  // @todo - this should be the zend decoder probably
  protected function _processContent( $str ){
    return json_decode( $str, true );
  }

  public function process( Core_API_Service $service ){
    $obj = new Core_Object();
    $obj->load($this->processContent( $this->getContent() ) );
    return $obj;
  }
}