<?php
class Assembla_API_Request extends Core_API_Request {

  protected function _setCache( $key, $value ){
        return;
  }
  protected function _getCache( $key ){
        return;
  }
  protected function _getCacheKey(){
        return '';
  }

  public function manageRequestData( $data ) {
    return $this;
  }

}
