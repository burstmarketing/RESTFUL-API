<?php

abstract class Assembla_Collection_Abstract extends Core_Collection {

  protected function _getModelClassName() {
    return 'Core_Model';
  }

  public function load(array $data) {
    $model_class = $this->_getModelClassName();

    $filtered_data = (array) $this->_filterData($data);

    // If $filtered_data is something like array('key' => 'val'), we need
    // to put it in array so it's like array(array('key' => 'val'))
    // -- There should be a neater way of doing this --
    if (sizeof($filtered_data) == 1 && !is_array(current($filtered_data))) {
      $filtered_data = array(array(key($filtered_data) => current($filtered_data)));
    }

    foreach ($filtered_data as $model_data) {
      $model = new $model_class;
      $model->load($model_data);
      $this->offsetSet('', $model);
    }

    return $this;
  }
}