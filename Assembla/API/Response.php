<?php
class Assembla_API_Response  extends Core_API_Response_XML {

  public function processRequest( $request, $classname = "Core_Object" ){
    $http_response = $request->send();
    $element = new Assembla_API_XML_Element( $http_response );
    if( ! count($element->xpath('error')) ){
      switch( $request->getType() ){
      case 'PUT':
      case 'POST':
      case 'DELETE':
        $message = new Core_Object;
        $message->setSuccess(1)->setBody( $element );
        return $message;
        break;
      default:
        $class = new $classname();
        return $class->load( $element );
        break;
      }
    } else {
      // this is not so great.  sorry.
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
  }
}

?>