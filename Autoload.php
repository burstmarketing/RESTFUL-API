<?php

final class RESTFUL_API_LOADER {

  public static $loader;
  const DIRECTORY_SEPARATOR = "/";

  public static function getBaseDir(){
  return dirname(__FILE__) . self::DIRECTORY_SEPARATOR;
  }

  public static function init(){
    require_once 'Zend/Loader/StandardAutoloader.php';
    self::$loader = new Zend\Loader\StandardAutoloader(array('autoregister_zf' => true));

    // register Zend as a prefix for backward's compatability
    self::$loader->registerPrefix('Zend', self::getBaseDir() . 'Zend');


    self::$loader->registerPrefix('Core', self::getBaseDir() . 'Core');
    self::$loader->registerPrefix('Assembla', self::getBaseDir() . 'Assembla');

    self::$loader->register();

  }

  public static function autoload($classname){
    $classFile = self::getBaseDir() . str_replace('_',self::DIRECTORY_SEPARATOR, $classname) . ".php";
    if( is_readable( $classFile ) && ! class_exists($classname) ):
      include_once( $classFile );
    endif;
  }

} RESTFUL_API_LOADER::init();




?>