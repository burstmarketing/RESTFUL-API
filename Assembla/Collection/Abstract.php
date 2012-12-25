<?php

abstract class Assembla_Collection_Abstract extends Core_Collection {

  protected function _getModelClassName() {
    return 'Core_Model';
  }

  protected function _getModelElementTag(){
    $model_name = $this->_getModelClassName();

    return $model_name::getTagName();
  }

  public function load(array $data) {
    $model_class = $this->_getModelClassName();

    $filtered_data = (array) $this->_filterData($data);

    foreach ($filtered_data as $model_data) {
      $model = new $model_class;
      $model->load($model_data);
      $this->offsetSet('', $model);
    }

    return $this;
  }
}