<?php

class Assembla_API_V1_Response  extends Core_API_Response_Json {

  public function processRequest(Core_API_Request $request, $classname = "Core_Object" ){
    $http_response = $request->send();
    $data          = json_decode($http_response, true);

    if (!isset($data['errors'])) {
      switch ($request->getType()) {
      case 'PUT':
      case 'POST':
      case 'DELETE':
        $message = new Core_Object;
        $message->setSuccess(1)
                ->setBody(new Core_Object($data));

        return $message;
        break;

      default:
        // make sure $classname exists
        $classname = (class_exists($classname)) ? $classname : 'Core_Object';
        $class = new $classname;
        return $class->setData($data);
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