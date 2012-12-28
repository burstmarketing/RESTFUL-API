<?php

class Core_Object {

    protected $_data = array();

    protected $_hasDataChanges = false;

    protected $_origData;


    public function __construct() {
      $args = func_get_args();

      if (empty($args[0])) {
	$args[0] = array();
      }

      $this->_data = $args[0];
    }

    public function hasDataChanges() {
      return $this->_hasDataChanges;
    }

    public function addData(array $arr) {
      foreach($arr as $index=>$value) {
	$this->setData($index, $value);
      }

      return $this;
    }

    public function setData($key, $value=null) {
      $this->_hasDataChanges = true;

      if(is_array($key)) {
	$this->_data = $key;
      } else {
	$this->_data[$key] = $value;
      }

      return $this;
    }

    public function unsetData($key=null) {
      $this->_hasDataChanges = true;

      if (is_null($key)) {
	$this->_data = array();
      } else {
	unset($this->_data[$key]);
      }

      return $this;
    }

    public function getData($key='', $index=null) {
      if ($key === '') {
	return $this->_data;
      }

      $default = null;

      // accept a/b/c as ['a']['b']['c']
      if (strpos($key,'/')) {
	$keyArr = explode('/', $key);
	$data = $this->_data;

	foreach ($keyArr as $i=>$k) {
	  if ($k==='') {
	    return $default;
	  }
	  if (is_array($data)) {
	    if (!isset($data[$k])) {
	      return $default;
	    }
	    $data = $data[$k];
	  } elseif ($data instanceof Varien_Object) {
	    $data = $data->getData($k);
	  } else {
	    return $default;
	  }
	}
	return $data;
      }

      // legacy functionality for $index
      if (isset($this->_data[$key])) {
	return $this->_data[$key];
      }

      return $default;
    }

    protected function _getData($key) {
      return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    public function __toArray(array $arrAttributes = array()) {
      if (empty($arrAttributes)) {
	$arrRes = array();
	foreach( $this->_data AS $_key => $_value ){
	  if( $_value instanceof Core_Object || $_value instanceof Core_Collection ){
	    $arrRes[$_key] = $_value->toArray();
	  } else {
	    $arrRes[$_key] = $_value;
	  }
	}
	return $arrRes;
      }

      // Note: this should be fixed so it calls toArray() on
      // any values that are models!
      // nevermind, #fixedd
      $arrRes = array();
      foreach ($arrAttributes as $attribute) {
	if (isset($this->_data[$attribute])) {
	  if( $this->_data[$attribute] instanceof Core_Object || $this->_data[$attribute] instanceof Core_Collection ){
	    $arrRes[$attribute] = $this->_data[$attribute]->toArray();
	  } else {
	    $arrRes[$attribute] = $this->_data[$attribute];
	  }
	} else {
	  $arrRes[$attribute] = null;
	}
      }

      return $arrRes;
    }


    public function toArray(array $arrAttributes = array()) {
      return $this->__toArray($arrAttributes);
    }

    protected function __toXml($arrData, array $arrAttributes = array()) {
      if( $arrData instanceof Core_Object || $arrData instanceof Core_Collection){
	return $arrData->toXml($arrAttributes);
      } else if( ! is_array($arrData) ) {
	return $arrData;
      } else {
	$xml = '';

	$xmlModel = new Core_API_XML_Element('<node></node>');

	foreach ($arrData as $fieldName => $fieldValue) {
	  if( empty( $arrAttributes) || in_array( $fieldName, $arrAttributes ) ){

	    if( is_string($fieldValue) ){
	      $fieldValue = $xmlModel->xmlentities($fieldValue);
	    }

	    $xml.= "<$fieldName>" . $this->__toXml($fieldValue) . "</$fieldName>"."\n";
	  }
	}
      }
      return $xml;
    }

    public function toXml($arrAttributes = array(), $rootName = '') {
      $xml = '';
      if ($rootName) {
	$xml.= '<'.$rootName.'>'."\n";
      }

      $xml .= $this->__toXml( $this->getData(), $arrAttributes );

      if ($rootName) {
	$xml.= '</'.$rootName.'>'."\n";
      }

      return $xml;
    }

    protected function __toJson(array $arrAttributes = array()) {
      $arrData = $this->toArray($arrAttributes);
      $json = json_encode($arrData);
      return $json;
    }

    public function toJson(array $arrAttributes = array()) {
      return $this->__toJson($arrAttributes);
    }

    public function toString($format='') {
      if (empty($format)) {
	$str = implode(', ', $this->getData());
      } else {
	preg_match_all('/\{\{([a-z0-9_]+)\}\}/is', $format, $matches);
	foreach ($matches[1] as $var) {
	  $format = str_replace('{{'.$var.'}}', $this->getData($var), $format);
	}
	$str = $format;
      }
      return $str;
    }

    public function __call($method, $args) {
      switch (substr($method, 0, 3)) {
      case 'get' :
	$key = $this->_underscore(substr($method,3));
	$data = $this->getData($key, isset($args[0]) ? $args[0] : null);
	return $data;

      case 'set' :
	$key = $this->_underscore(substr($method,3));
	$result = $this->setData($key, isset($args[0]) ? $args[0] : null);
	return $result;

      case 'uns' :
	$key = $this->_underscore(substr($method,3));
	$result = $this->unsetData($key);
	return $result;

      case 'has' :
	$key = $this->_underscore(substr($method,3));
	return isset($this->_data[$key]);
      }
      throw new Exception("Invalid method ".get_class($this)."::".$method."(".print_r($args,1).")");
    }

    public function isEmpty() {
      return empty($this->_data);
    }

    protected function _underscore($name) {
      return strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
    }

    protected function _camelize($name) {
      return uc_words($name, '');
    }

    public function getOrigData($key=null) {
      if (is_null($key)) {
	return $this->_origData;
      }
      return isset($this->_origData[$key]) ? $this->_origData[$key] : null;
    }

    public function setOrigData($key=null, $data=null) {
      if (is_null($key)) {
	$this->_origData = $this->_data;
      } else {
	$this->_origData[$key] = $data;
      }
      return $this;
    }

    public function dataHasChangedFor($field) {
      $newData = $this->getData($field);
      $origData = $this->getOrigData($field);
      return $newData!=$origData;
    }
  }