<?php


class Assembla_API extends Core_API {


  protected function _getRequest(){
	return new Assembla_API_Request;
  }

  protected function _processRequest( Core_API_Request $request){
	$url = $this->getConfig('defaults/url');

	return $request;
  }
  

  }


?>