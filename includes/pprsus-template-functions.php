<?php
/**
 * globally accessible functions
 */
if(!defined('ABSPATH')){ exit; }

function pprsus_get_template($template_name){
  return (new PPRSUS_Template_Functions)->find_template($template_name);
}

if(!class_exists('PPRSUS_Template_Functions')){
  class PPRSUS_Template_Functions{

    public function load_template($template){
      $template_name = '';

      //if(is_page('worksheet')){
      //  $template_name = 'page-worksheet.php';
      //}

      if($template_name != ''){
        return $this->find_template($template_name);
      }

      return $template;
    }//end load_template()

    public function find_template($template_name){
      $template_path = get_stylesheet_directory_uri() . '/pprsus-prison-match-templates/';

      $template = locate_template(array(
        $template_path . $template_name,
        $template_name
      ), true);

      if(!$template){
        $template = PPRSUS_PLUGIN_DIR . '/templates/' . $template_name;
      }

      return $template;
    }//end find_template()
  }//end class
}