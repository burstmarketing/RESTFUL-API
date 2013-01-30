<?php

use Zend\Stdlib;

class Assembla_API_Client extends Zend\Http\Client {
  /**
   * A Little hack to get our dispatch function to return the type of object that we want
   * I'm sure if i had a better idea how to do namespacing this could be cleaned up. -Chris
   **/

  public function dispatch(Stdlib\RequestInterface $request, Stdlib\ResponseInterface $response = null)
  {

    if( $request->useCache() && $request->_getCache($request->_getCacheKey()) ) {
      return Assembla_API_V1_Response::fromString( $request->_getCache( $request->_getCacheKey() ) );
    } else {
      $this->send($request);
      $response = Assembla_API_V1_Response::fromString( $this->lastRawResponse );
      return $response;
    }
  }

}