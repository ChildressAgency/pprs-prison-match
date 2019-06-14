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

    public function add_acf_options_page(){
      acf_add_options_page(array(
        'page_title' => esc_html__('Prison Match Form Settings', 'pprsus'),
        'menu_title' => esc_html__('Prison Match Form Settings', 'pprsus'),
        'menu_slug' => 'prison-match-form-settings',
        'capability' => 'edit_posts',
        'redirect' => false
      ));
    }

  }//end class
}