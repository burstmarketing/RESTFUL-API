<?php
  // Response objects take xml and return a class that 
  // Represents that xml object.  
abstract class Core_API_Response extends Core_Object {

  abstract public function processRequest( $http_response, $classname = "Core_Object" );

  }
?>