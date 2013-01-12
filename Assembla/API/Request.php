<?php
class Assembla_API_Request extends Core_API_Request_XML {
  protected $_api;

  protected function _processURI( $uri, $args = array() ){

    if( empty($args) ):
      return $uri;
    endif;

    $callback = function($matches) use ($args) {
      $propertyName = $matches[1];
      if ( !array_key_exists( $propertyName, $args)) {
        throw new Exception( $propertyName . " not passed into _parseVars function. ");
      }

      $propertyValue = $args[$propertyName];

      if (is_bool($propertyValue)) {
        if ($propertyValue === true) {
          $propertyValue = "true";
        } else {
          $propertyValue = "false";
        }
      }
      return $propertyValue;
    };


    while (strpos($uri, '${') !== false) {
      $uri = preg_replace_callback('/\$\{([^\$}]+)\}/', $callback, $uri);
    }
    return $uri;

  }

  protected function _getURIArgs( $uri, $args ){
    $matches = array();
    preg_match_all('/\$\{([^\$}]+)\}/', $uri, $matches);

    if( isset($matches[1]) ){
      $vals = array_slice( $args, 0, count($matches[1]) );
      return array_combine( $matches[1], $vals );
    }

    return array();
  }




  public function getAPI(){
    return $this->_api;
  }

  // @td should do typ checking here
  public function setAPI( $api ) {
    $this->_api = $api;
    return $this;
  }



  public function generateRequest( $service, $args ){


    if( $service->key ){
      $this->setKey( $service->key );
    }

    if( $service->url ){
      $this->setUrl( $service->url );
    }


    if( isset( $service->uri ) ){

      // Handle $args - this can be passed in as a
      // single array with key => value pairs to be
      // sent to processURI  or $args can just be a
      // list of arguments in wich case we add the
      // keys to the arguments with _getURIArgs()
      // NOTE: this should probably be reworked to simply
      //       validate the arguments and set the uri
      //       in a protected function.
      if( ! empty( $args ) ){
        if( is_array( $args[0] ) ){
          $_args = $args[0];
        } else {
          $_args =  $this->_getURIArgs( $service->uri, $args );
        }
        $this->setUri( $this->_processURI( $service->uri, $_args ) );
      } else {
        // no arguments,  just set the uri.
        $this->setUri( $service->uri );
      }

      // set curl data on this request
      if( isset($args[1]) && is_string( $args[1] ) ){
        $this->setCurlData( $args[1] );
      }

    } else {
      throw new Exception("could not find 'uri' in service: " . $key );
    }

    if( isset( $service->type ) ){
      $this->setType( $service->type );
    } else {
      throw new Exception("type is not defined in service: " . $key );
    }


    $this->setUsername( $this->getAPI()->getConfig('credentials/username') );
    $this->setPassword( $this->getAPI()->getConfig('credentials/password') );


    if( isset( $service->headers ) ){
      foreach( $service->headers AS $header ){
        $this->addHeader( $header );
      }
    }

    return $this;

  }

}
?>