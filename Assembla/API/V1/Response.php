<?php

class Assembla_API_V1_Response  extends Core_API_Response_Json {

  public function setFilters(array $filters) {
    $this->_filters = $filters;
    return $this;
  }

  public function clearFilters() {
    $this->_filters = array();

    return $this;
  }


  public function process( Core_API_Service $service ){
    $classname = $service->getClassname();
    // @todo - this should be the zend decoder probably
    $data = json_decode($this->getContent(), true);


    // this whole thing is going to have to be rewritten
    if (!isset($data['errors'])) {
      switch ($service->getType()) {
      case 'PUT':
      case 'POST':
      case 'DELETE':
        $obj = new $classname;

        if (method_exists($obj, 'load')) {
          $obj->load($data);
        } else {
          $obj->setData($data);
        }

        $message = new Core_Object;
        $message->setSuccess(1)
                ->setBody($obj);

        return $message;
        break;

      default:
        // make sure $classname exists
        $classname = (class_exists($classname)) ? $classname : 'Core_Object';
        $class = new $classname;

        // Pass filters to the proper collection/model/whatever, they're
        // only on the API object temporarily.
        $class->setFilters($this->getFilters);
        $this->clearFilters();

        if (method_exists($class, 'load')) {
          return $class->load($data);
        } else {
          return $class->setData($data);
        }
        break;
      }
      } else {
        $error = "Errors were encountered: \n";

        foreach ($data['errors'] as $e) {
          if ($e == 'HTTP Basic: Access denied.') {
            throw new Core_Exception_Auth('Authentication credentials failed.');
          } else {
            $error .= ' ' . $e . ' ';
          }
        }

        throw new Assembla_Exception($error);
      }

  }

}