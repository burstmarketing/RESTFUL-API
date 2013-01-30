<?php

class Assembla_API_V1_Response  extends Assembla_API_Response {

  protected function _processContent() {

    try {
      return Zend\Json\Json::decode( $this->getContent(), Zend\Json\Json::TYPE_ARRAY );
    } catch( Zend\Json\Exception\RuntimeException $e ) {
      try {
        $xml_reader = new Zend\Config\Reader\Xml();
        return $xml_reader->fromString( $this->getContent() );
      } catch (Exception $e ) {
        throw new Assembla_Exception( "Server Responded with unformated data: " . substr( $this->getContent(), 0, 50) . strlen($this->getContent()) > 50 ? "..." : "" );
      }
    }
  }


  public function getObject( Core_API_Service $service, $filters = array() ){

    if( $this->isSuccess() ){
      $classname = $service->getClassname() ? $service->getClassname() : "Core_Object";
      $data_pre_array = $this->_processContent();
      #--
      $data = (array) $data_pre_array;

      $class = new $classname();
      $class->setFilters($filters);

      return method_exists($class, 'load') ? $class->load($data) : $class->setData( $data );

    } else {
      throw new Assembla_Exception( "Request error: " . $this->renderStatusLine() );
    }

  }

}