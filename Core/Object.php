<?php
  // Boofed wholesale from Magento's Varien_Object
class Core_Object {

    protected $_data = array();

    protected $_hasDataChanges = false;

    protected $_origData;


    public function __construct()
    {
        $args = func_get_args();
        if (empty($args[0])) {
            $args[0] = array();
        }
        $this->_data = $args[0];

    }
    public function hasDataChanges()
    {
        return $this->_hasDataChanges;
    }

    public function addData(array $arr)
    {
        foreach($arr as $index=>$value) {
            $this->setData($index, $value);
        }
        return $this;
    }

    public function setData($key, $value=null)
    {
        $this->_hasDataChanges = true;
        if(is_array($key)) {
            $this->_data = $key;
        } else {
            $this->_data[$key] = $value;
        }
        return $this;
    }

    public function unsetData($key=null)
    {
        $this->_hasDataChanges = true;
        if (is_null($key)) {
            $this->_data = array();
        } else {
            unset($this->_data[$key]);
        }
        return $this;
    }

    public function getData($key='', $index=null)
    {
        if (''===$key) {
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

    protected function _getData($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }


    public function __toArray(array $arrAttributes = array())
    {
        if (empty($arrAttributes)) {
            return $this->_data;
        }

        $arrRes = array();
        foreach ($arrAttributes as $attribute) {
            if (isset($this->_data[$attribute])) {
                $arrRes[$attribute] = $this->_data[$attribute];
            }
            else {
                $arrRes[$attribute] = null;
            }
        }
        return $arrRes;
    }


    public function toArray(array $arrAttributes = array())
    {
        return $this->__toArray($arrAttributes);
    }

	/* uncomment when we port over Varien_Simplexml_Element

    protected function __toXml(array $arrAttributes = array(), $rootName = 'item', $addOpenTag=false, $addCdata=true)
    {
        $xml = '';
        if ($addOpenTag) {
            $xml.= '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        }
        if (!empty($rootName)) {
            $xml.= '<'.$rootName.'>'."\n";
        }
        $xmlModel = new Varien_Simplexml_Element('<node></node>');
        $arrData = $this->toArray($arrAttributes);
        foreach ($arrData as $fieldName => $fieldValue) {
            if ($addCdata === true) {
                $fieldValue = "<![CDATA[$fieldValue]]>";
            } else {
                $fieldValue = $xmlModel->xmlentities($fieldValue);
            }
            $xml.= "<$fieldName>$fieldValue</$fieldName>"."\n";
        }
        if (!empty($rootName)) {
            $xml.= '</'.$rootName.'>'."\n";
        }
        return $xml;
    }

    public function toXml(array $arrAttributes = array(), $rootName = 'item', $addOpenTag=false, $addCdata=true)
    {
        return $this->__toXml($arrAttributes, $rootName, $addOpenTag, $addCdata);
    }

*/


    protected function __toJson(array $arrAttributes = array())
    {
        $arrData = $this->toArray($arrAttributes);
        $json = json_encode($arrData);
        return $json;
    }


    public function toJson(array $arrAttributes = array())
    {
        return $this->__toJson($arrAttributes);
    }


    public function toString($format='')
    {
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



    public function __call($method, $args)
    {
        switch (substr($method, 0, 3)) {
            case 'get' :
                //Varien_Profiler::start('GETTER: '.get_class($this).'::'.$method);
                $key = $this->_underscore(substr($method,3));
                $data = $this->getData($key, isset($args[0]) ? $args[0] : null);
                //Varien_Profiler::stop('GETTER: '.get_class($this).'::'.$method);
                return $data;

            case 'set' :
                //Varien_Profiler::start('SETTER: '.get_class($this).'::'.$method);
                $key = $this->_underscore(substr($method,3));
                $result = $this->setData($key, isset($args[0]) ? $args[0] : null);
                //Varien_Profiler::stop('SETTER: '.get_class($this).'::'.$method);
                return $result;

            case 'uns' :
                //Varien_Profiler::start('UNS: '.get_class($this).'::'.$method);
                $key = $this->_underscore(substr($method,3));
                $result = $this->unsetData($key);
                //Varien_Profiler::stop('UNS: '.get_class($this).'::'.$method);
                return $result;

            case 'has' :
                //Varien_Profiler::start('HAS: '.get_class($this).'::'.$method);
                $key = $this->_underscore(substr($method,3));
                //Varien_Profiler::stop('HAS: '.get_class($this).'::'.$method);
                return isset($this->_data[$key]);
        }
        throw new Varien_Exception("Invalid method ".get_class($this)."::".$method."(".print_r($args,1).")");
    }


    public function isEmpty()
    {
        if (empty($this->_data)) {
            return true;
        }
        return false;
    }

    protected function _underscore($name)
    {
        $result = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
        return $result;
    }

    protected function _camelize($name)
    {
        return uc_words($name, '');
    }

    public function serialize($attributes = array(), $valueSeparator='=', $fieldSeparator=' ', $quote='"')
    {
        $res  = '';
        $data = array();
        if (empty($attributes)) {
            $attributes = array_keys($this->_data);
        }

        foreach ($this->_data as $key => $value) {
            if (in_array($key, $attributes)) {
                $data[] = $key . $valueSeparator . $quote . $value . $quote;
            }
        }
        $res = implode($fieldSeparator, $data);
        return $res;
    }


    public function getOrigData($key=null)
    {
        if (is_null($key)) {
            return $this->_origData;
        }
        return isset($this->_origData[$key]) ? $this->_origData[$key] : null;
    }


    public function setOrigData($key=null, $data=null)
    {
        if (is_null($key)) {
            $this->_origData = $this->_data;
        } else {
            $this->_origData[$key] = $data;
        }
        return $this;
    }

    public function dataHasChangedFor($field)
    {
        $newData = $this->getData($field);
        $origData = $this->getOrigData($field);
        return $newData!=$origData;
    }



  }


?>