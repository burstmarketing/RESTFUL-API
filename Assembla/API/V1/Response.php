<?php

// @todo shouldn't this extend Core_API_Response_Json?
class Assembla_API_V1_Response  extends Core_API_Response_XML {

  public function processRequest( Assembla_API_V1_Request $request, $classname = "Core_Object" ){
        $http_response = $request->send();
        $data = json_decode( $http_response, true );
        if( ! isset($data['errors']) ){
          switch( $request->getType() ){
          case 'PUT':
          case 'POST':
          case 'DELETE':
            $message = new Core_Object;
            $message->setSuccess(1)->setBody( new Core_Object($data) );
            return $message;
            break;
          default:
            $class = new $classname();
            return $class->setData( $data );
            break;
          }
        } else {
          // this is wrong,  need to get some errors to fix it,  definately
          $err = "Errors were encountered:";
          foreach( $data['errors'] as $e ){
            if( $e == "HTTP Basic: Access denied." ){
              throw new Core_Exception_Auth( "Authentical credentials failed!" );
            } else {
              $err .= " " . $e . " ";
            }
          }
          throw new Exception( $err );
        }
  }
}