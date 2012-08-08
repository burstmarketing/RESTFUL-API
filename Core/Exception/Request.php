<?php
class Core_Exception_Request extends Exception {

  public function __construct($message, $code = 0) {
    parent::__construct($message, $code);
  }

}