<?php

class Assembla_Model_Wikipage extends Assembla_Model_Abstract {

  public function load($element) {
    return parent::load($element);
  }

  public function hasParentPage() {
    return (bool) $this->getParentId();
  }
}