<?php
/**
 * MultiStep Worksheet
 */
if(!defined('ABSPATH')){ exit; }

if(!class_exists('PPRSUS_MultiStep_Worksheet')){
  class PPRSUS_MultiStep_Worksheet{
    private $form_id;
    private $form_post_type;
    private $step_ids;
    private $form_post_id;
    private $defendant_id;

    public function __construct(){
      $this->form_id = 'pprsus-worksheet';
      $this->form_post_type = $this->get_form_post_type();
      $this->step_ids = $this->get_form_step_ids();
      $this->form_post_id = $this->get_form_post_id();
      $this->defendant_id = $this->get_defendant_id();

      add_action('acf/validate_save_post', array($this, 'skip_validation'), 10, 0);
      add_action('acf/save_post', array($this, 'process_acf_form'), 20);
    }

    public function get_form_post_id(){
      if(isset($_GET['post_id'])){
        if($this->requested_post_is_valid() && $this->is_valid_token()){
          return (int)$_GET['post_id'];
        }
        else{
          $this->send_to_dashboard();
        }
      }

      return 'new_post';
    }

    public function get_defendant_id(){
      if(isset($_GET['defendant_id'])){
        //TODO: check to see if current user can modify defendant
        return $_GET['defendant_id'];
      }

      return 'new_defendant';
    }

    public function get_form_step_ids(){
      $form_steps = array();

      global $wpdb;
      $groups = $wpdb->get_results($wpdb->prepare("
        SELECT post_name
        FROM {$wpdb->prefix}posts
        WHERE post_type = %s
          AND post_content LIKE '%%%s%%'
          ORDER BY menu_order ASC", 'acf-field-group', $this->form_post_type));

      $g = 0;
      foreach($groups as $group){
        $form_steps[$g] = $group->post_name;
        $g++;
      }

      return $form_steps;
    }

    public function get_form_post_type(){
      if(isset($_GET['form_type'])){
        $form_type = sanitize_text_field($_GET['form_type']);
        if($this->is_valid_form_type($form_type)){
          return $form_type;
        }
      }

      $this->send_to_dashboard();
    }

    private function is_valid_form_type($form_type){
      $valid_form_types = array(
        'defendants',
        'medical_history',
        'security'
      );

      if(in_array($form_type, $valid_form_types)){
        return true;
      }

      return false;
    }

    private function requested_post_is_valid(){
      return (get_post_type((int)$_GET['post_id']) === $this->form_post_type && get_post_status((int)$_GET['post_id']) === 'publish');
    }

    private function is_valid_token(){
      if(!isset($_GET['token'])){ return false; }

      $token_from_url = sanitize_text_field($_GET['token']);
      $token_from_post_meta = get_post_meta((int)$_GET['post_id'], 'secret_token', true);

      if($token_from_url === $token_from_post_meta){
        return true;
      }

      return false;
    }

    public function send_to_dashboard(){
      wp_safe_redirect(esc_url(home_url('dashboard')));
      exit();
    }
  }
}