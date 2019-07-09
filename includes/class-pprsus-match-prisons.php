<?php
/**
 * Match the Prisons using the Security score information
 */
if(!defined('ABSPATH')){ exit; }

if(!class_exists('PPRSUS_Match_Prisons')){
  class PPRSUS_Match_Prisons{

    private $user_id;
    private $security_form_id;
    private $defendant_id;
    private $token;
    private $error_message = '';

    public function __construct(){
      $this->user_id = get_current_user_id();
      $this->security_form_id = get_query_param('post_id');
      $this->defendant_id = get_query_param('defendant_id');
      $this->token = get_query_param('token');
      
      $this->output_prison_list();
    }

    public function output_prison_list(){
      if(!allowed_to_view_matches()){
        echo $this->error_message;
        return;
      }


    }

    private function allowed_to_view_matches(){

    }

    public function get_query_param($param){
      if(isset($_GET[$param]) && $_GET[$param] != ''){
        return $_GET[$param];
      }

      return false;
    }
  }//end class
}