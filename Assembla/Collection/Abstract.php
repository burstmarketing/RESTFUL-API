<?php

abstract class Assembla_Collection_Abstract extends Core_Collection {

  protected $_filters = array();

  public function getFilters(){
    return $this->_filters;
  }

  protected function _setFilters( $filters ){
    $this->_filters = $filters;
    return $this;
  }

  protected function _getModelElementTag(){
    $model_name = $this->_getModelClassName();
        return $model_name::getTagName();
  }

  protected function _getModelClassName(){
        // This is almost certainly NOT what we want
        // please override this function in inherited
        // classes
        return "Core_Model";
  }

  public function load(array $data) {
    $model_class = $this->_getModelClassName();

    foreach ($this->_filterData($data) as $model_data) {
      $model = new $model_class;
      $model->load($model_data);
      $this->offsetSet('', $model);
    }

    return $this;
  }

  protected function _filterData(array $data) {
    foreach ($data as $key => &$val) {
      foreach ($this->_filters as $_func_name => $_args) {
        /* Throw $val at the beginning of $_args so the filter
           method gets the model data, and the args */
        array_unshift($_args, $val);
        if (!call_user_func_array(array($this, $_func_name), $_args)) {
          unset($data[$key]);
        }
      }
    }

    return $data;
  }
}