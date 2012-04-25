<?php
abstract class Core_Collection implements ArrayAccess, Iterator, Countable {

  private $_collection = array();
  private $_limit = -1; // -1 means all


  abstract protected function _getModelClassName();

  public function reverse(){
	$this->_collection = array_reverse( $this->_collection);
	return $this;
  }

  public function limit( $num ){
	$this->_limit = $num;
	return $this;
  }

  public function toXml($arrData, array $arrAttributes = array(), $addCdata=false){
	$xml = '';
	foreach ($this as $item) {
	  $xml.=$item->toXml($arrData, $arrAttributes, $addCdata);
	}	
	return $xml;
  }

  public function toArray($arrRequiredFields = array())
  {

	$arrItems = array();
	if( $this->_limit === -1 ){
	  foreach ($this as $item) {
		$arrItems[] = $item->toArray($arrRequiredFields);
	  }
	} else {
	  for( $i = 0; $i < $this->_limit; $i++ ){
		$arrItems[] = $this->_collection[$i]->toArray($arrRequiredFields);
	  }
	}
	return $arrItems;
  }

  public function toJSON($arrRequiredFields = array())
  {
	return json_encode( $this->toArray($arrRequiredFields) );
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