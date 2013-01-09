<?php
class Core_Exception_Auth extends Exception {

  public function __construct($message, $code = 0) {
    parent::__construct($message, $code);
  }

}