<?php
class Core_API_XML_Helper {


  static public function hyphenToUnderscoreKeys( &$arr ){
  if( ! is_array( $arr ) ){
    return $arr;
  }

  $_arr = array();
  foreach( $arr AS $key => $value ){
    $_key = preg_replace('/-/',"_", $key );
    $_arr[$_key] = self::hyphenToUnderscoreKeys( $value );

  }
  return $_arr;

  }

  static public function underscoreToHyphenKeys( $arr ){
  if( ! is_array( $arr ) ){
    return $arr;
  }

  $_arr = array();
  foreach( $arr AS $key => $value ){
    $_key = preg_replace('/_/',"-", $key );
    $_arr[$_key] = self::underscoreToHyphenKeys( $value );

  }
  return $_arr;

  }


  }

?>