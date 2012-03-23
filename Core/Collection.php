<?php
abstract class Core_Collection implements ArrayAccess, Iterator, Countable {

  private $_collection = array();
  
  abstract protected function _getModelClassName();


  public function toXml()
  {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <collection>  
           <totalRecords>'.$this->_totalRecords.'</totalRecords>
           <items>';

        foreach ($this as $item) {
		  $xml.=$item->toXml();
        }
        $xml.= '</items>
        </collection>';
        return $xml;
  }

  public function toArray($arrRequiredFields = array())
  {

	$arrItems = array();
	foreach ($this as $item) {
	  $arrItems[] = $item->toArray($arrRequiredFields);
	}
	return $arrItems;
  }

  public function toJSON($arrRequiredFields = array())
  {
	$_items = array();
	foreach ($this as $item) {
	  $_items[] = $item->toArray($arrRequiredFields);
	}
	return json_encode( $_items );
  }



  public function offsetSet($offset,$value) {
	$className = $this->_getModelClassName();
	if ($value instanceof $className){
	  if ($offset == "") {
		$this->_collection[] = $value;
	  }else {
		$this->_collection[$offset] = $value;
	  }
	} else {
	  throw new Exception ("Value must be of type " . $className);
	}
  }

  public function offsetExists($offset) {
	return isset($this->_collection[$offset]);
  }

  public function offsetUnset($offset) {
	unset($this->_collection[$offset]);
  }

  public function offsetGet($offset) {
	return isset($this->_collection[$offset]) ? $this->_collection[$offset] : null;
  }

  public function rewind() {
	reset($this->_collection);
  }

  public function current() {
	return current($this->_collection);
  }

  public function key() {
	return key($this->_collection);
  }

  public function next() {
	return next($this->_collection);
  }

  public function valid() {
	return $this->current() !== false;
  }

  public function count() {
	return count($this->_collection);
  }

  }
?>