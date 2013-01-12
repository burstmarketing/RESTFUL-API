<?php

abstract class Core_API_Response extends Zend\Http\Response {
  abstract public function process( Core_API_Service $service );
}