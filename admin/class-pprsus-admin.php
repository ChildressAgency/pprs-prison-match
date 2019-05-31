<?php
/**
 * admin specific functions
 */
if(!defined('ABSPATH')){ exit; }

if(!class_exists('PPRSUS_Admin')){
  class PPRSUS_Admin{
    public function __construct(){
      //$this->load_dependencies();
    }

    public function acf_setting_path($path){
      $path = plugin_dir_path(__FILE__) . 'vendors/advanced-custom-fields-pro';
      return $path;
    }//end acf_setting_path

    public function acf_setting_dir($dir){
      $dir = plugin_dir_url(__FILE__) . 'vendors/advanced-custom-fields-pro';
      return $dir;
    }//end acf_setting_dir

  }//end class
}