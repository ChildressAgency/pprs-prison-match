<?php
/**
 * MultiStep Worksheet
 * 
 * Handles all worksheets breaking them up into steps.
 */
if(!defined('ABSPATH')){ exit; }

if(!class_exists('PPRSUS_MultiStep_Worksheet')){
  class PPRSUS_MultiStep_Worksheet{

    /**
     * Identifies this specific front end form
     * 
     * @var string
     */
    private $form_id;

    /**
     * Post type for this form
     * 
     * @var string
     */
    private $form_post_type;

    /**
     * The step ids - these are the ACF group ids,
     * each group is one step
     * 
     * @var array
     */
    private $step_ids;

    /**
     * The post id for this form - 
     * Either the post id or 'new_post'
     * 
     * @var string   
     */
    private $form_post_id;

    /**
     * The defendant id - if its a new defendant post type
     * value will be 'new_defendant'. Otherwise the defendant id
     * will come from the url param.
     * 
     * @var string
     */
    private $defendant_id;

    private $user_id;

    public function __construct(){
      $this->form_id = 'pprsus-worksheet';
      $this->form_post_type = $this->get_form_post_type();
      $this->step_ids = $this->get_form_step_ids();
      $this->form_post_id = $this->get_form_post_id();  //the post id or new_post
      $this->defendant_id = $this->get_defendant_id();  //the defendant id or new_defendant
      $this->user_id = get_current_user_id();

      //$this->output_acf_form();
      //add_action('init', array($this, 'output_acf_form'));
      //add_action('acf/validate_save_post', array($this, 'validate_form'), 10, 0);
      //add_action('acf/save_post', array($this, 'process_acf_form'), 20);
    }

    /**
     * Output the acf form for the post type
     */
    public function output_acf_form($args = []){
      if(!function_exists('acf_form')){ return; }

      if(!$this->can_continue_requested_worksheet()){ $this->send_to_dashboard(); return; }

      //requested step - checks $_POST first then $_GET,
      //if neither will be 1
      $requested_step = $this->get_requested_step();

      $args = wp_parse_args(
        $args,
        array(
          'post_id' => $this->form_post_id,
          'step' => 'new_post' === $this->form_post_id ? 1 : $requested_step,
          'post_type' => $this->form_post_type,
          'post_status' => $this->validate_form() ? 'publish' : 'draft',
        )
      );

      if($this->current_worksheet_is_finished()){ //is $_GET['finished'] set?
        $current_step_group = $this->step_ids;
        $submit_label = esc_html__('Update', 'pprsus');
        $submit_button = '<input type="submit" class="acf-button button button-primary button-large" value="%s" />';
      }
      else{
        $current_step_group = array(($args['post_id'] !== 'new_post' && $args['step'] > 1) ? $this->step_ids[(int)$args['step'] - 1] : $this->step_ids[0]);
        $submit_label = $args['step'] < count($this->step_ids) ? esc_html__('Save and Continue', 'pprsus') : esc_html__('Review and Finish', 'pprsus');
        $submit_button = '<input type="submit" class="acf-button button button-primary button-large acf-hidden" value="%s" />';
      }

      //show the progress bar
      $this->display_form_nav($args);

      //show the acf form
      $form_args = array(
        'id' => $this->form_id,
        'post_id' => $args['post_id'],
        'new_post' => array(
          'post_type' => $args['post_type'],
          'post_status' => $args['post_status']
        ),
        'field_groups' => $current_step_group,
        'submit_value' => $submit_label,
        'html_submit_button' => $submit_button,
        'html_after_fields' => $this->output_hidden_fields($args)
      );
      acf_form($form_args);
    }

    /**
     * output html and hidden fields at end of acf_form
     */
    private function output_hidden_fields($args){
      $inputs = array();
      
      //fix issue where custom buttons try to show inline with validation messages
      $inputs[] = '<div class="clearfix"></div>';

      $inputs[] = sprintf('<input type="hidden" name="pprsus-form-id" value="%1$s" />', $this->form_id);
      $inputs[] = isset($args['step']) ? sprintf('<input type="hidden" name="pprsus-current-step" value="%1$s" />', $args['step']) : '';

      if(!$this->current_worksheet_is_finished()){ //not on the finish/validation stage of form
        //don't show previous button if we're on the first step of form
        if($this->get_requested_step() != 1){
          $inputs[] = sprintf('<input type="button" id="pprsus-previous" name="previous" class="acf-button button button-primary button-large pprsus-submit" value="%1$s" />', esc_html__('Previous', 'pprsus'));
        }

        //don't show the next button if we're on the last step of the form
        if($args['step'] < count($this->step_ids)){
          $inputs[] = sprintf('<input type="button" id="pprsus-next" name="next" class="acf-button button button-primary button-large pprsus-submit" value="%1$s" />', esc_html__('Next', 'pprsus'));
        }

        //allows user to jump to finish/validation stage
        $inputs[] = sprintf('<input type="button" id="pprsus-finish" name="finish" class="acf-button button button-primary button-large pprsus-submit" value="%1$s" />', esc_html__('Review and Finish', 'pprsus'));
      }
      else{ //form is finished we're at the validation stage, show a link to the dashboard
        $inputs[] = sprintf('<div class="btn-wrapper"><a href="%1$s" class="btn">%2$s</a></div>', esc_url(home_url('dashboard')), esc_html__('&lt; Back to Dashboard', 'pprsus'));
      }

      $inputs[] = sprintf('<input type="button" id="pprsus-finish-later" name="saveforlater" class="acf-button button button-primary button-large pprsus-submit" value="%1$s" />', esc_html__('Save for Later', 'pprsus'));

      //this hidden field is updated by javascript so app know whether to skip validation
      $inputs[] = '<input type="hidden" id="direction" name="direction" value="" />';

      return implode(' ', $inputs);
    }

    private function display_form_nav($args){
      $number_of_steps = count($this->step_ids);
      $current_step = $args['step'];

      echo '<div class="worksheet-nav"><ul class="nav-tabs">';

      for($i = 1; $i <= $number_of_steps; $i++){
        $form_letter = $this->get_form_letter();

        if($i == $current_step){
          echo '<li><span class="worksheet_nav_selected">' . $form_letter . $i . '</span></li>';
        }
        else{
          if($args['post_id'] == 'new_post' || !$this->is_valid_token()){
            echo '<li><span class="disabled">' . $form_letter . $i . '</span></li>';
          }
          else{
            $nav_args = array(
               'post_id' => $args['post_id'],
               'defendant_id' => $this->defendant_id,
               'token' => $_GET['token'],
               'step' => $i,
               'form_type' => $this->form_post_type
            );

            echo '<li><a href="' . add_query_arg($nav_args, home_url('worksheet')) . '" class="button-primary">' . $form_letter . $i . '</a></li>';
          }
        }
      }

      echo '</ul></div><div class="clearfix"></div>';
    }

    private function get_form_letter(){
      switch($this->form_post_type){
        case 'defendants':
          return 'P';
        break;

        case 'medical_history':
          return 'M';
        break;

        case 'security':
          return 'S';
        break;

        default:
          return 'F';
      }
    }

    public function process_acf_form($post_id){
      if(is_admin() || !isset($_POST['pprsus-form-id']) || $_POST['pprsus-form-id'] != $this->form_id){
        return;
      }

      //if(!$this->can_continue_requested_worksheet()){ return; }

      $current_step = $this->get_requested_step();
      $defendant_info = $this->get_defendant_info($post_id);

      if($current_step == 1 && !isset($_GET['post_id'])){
        wp_update_post(array(
          'ID' => $post_id,
          'post_type' => $this->form_post_type,
          'post_title' => $defendant_info['defendant_name'],
          'post_author' => get_current_user_id()
        ));

        $token = wp_generate_password(rand(10,20), false, false);
        update_post_meta((int)$post_id, 'secret_token', $token);
        update_post_meta((int)$post_id, 'defendant_id', $defendant_info['defendant_id']);
        update_post_meta((int)$post_id, 'date_created', date('F j, Y'));
      }

      $query_args = array(
        'post_id' => $post_id,
        'token' => isset($token) ? $token : $_GET['token'],
        'defendant_id' => $defendant_info['defendant_id'],
        'form_type' => $_GET['form_type']
      );

      if(isset($_POST['direction'])){
        switch($_POST['direction']){
          case 'saveforlater':
            wp_safe_redirect(home_url('dashboard'));
            exit();
          break;

          case 'previous':
            $query_args['step'] = --$current_step;
          break;

          case 'next':
            $query_args['step'] = ++$current_step;
          break;

          case 'finish':
            $query_args['step'] = 0;
            $query_args['finished'] = 1;
          break;

          default:
            $query_args['step'] = $current_step;
        }
      }

      //if($current_step >= count($this->step_ids)){
      //  $query_args['finished'] = 1;
      //}

      $redirect_url = add_query_arg($query_args, wp_get_referer());
      wp_safe_redirect($redirect_url);
      exit();
    }

    private function get_defendant_info($post_id){
      if(isset($_GET['defendant_id']) && $this->form_post_type != 'defendants'){
        $defendant_id = sanitize_text_field($_GET['defendant_id']);
      }
      else{
        $defendant_id = $post_id;
      }

      $defendant_info['defendant_id'] = $defendant_id;

      if($defendant_id == $post_id){ //we're dealing with defendant post type
        $defendant_fname = esc_html(get_field('first_name', $post_id));
        $defendant_lname = esc_html(get_field('last_name', $post_id));

        $defendant_name = $defendant_fname . ' ' . $defendant_lname;
      }
      else{
        $defendant_name = esc_html(get_the_title($defendant_id));
      }

      $defendant_info['defendant_name'] = $defendant_name;

      return $defendant_info;
    }

    public function get_form_post_id(){
      if(isset($_GET['post_id'])){
        return (int)$_GET['post_id'];
      }

      return 'new_post';
    }

    public function get_defendant_id(){
      if(isset($_GET['defendant_id'])){
        if($this->form_post_type == 'defendants'){
          if($this->form_post_id == 'new_post'){
            return 'new_defendant';
          }
          else{
            return $this->form_post_id;
          }
        }
        else{
          return $_GET['defendant_id'];
        }
      }

      return 'new_defendant';
    }

    private function get_requested_step(){
      if(isset($_GET['finished']) && $_GET['finished'] == 1){
        return 0;
      }
      if(isset($_POST['pprsus-current-step']) && absint($_POST['pprsus-current-step']) <= count($this->step_ids)){
        return absint($_POST['pprsus-current-step']);
      }
      elseif(isset($_GET['step']) && absint($_GET['step']) <= count($this->step_ids)){
        return absint($_GET['step']);
      }

      return 1;
    }

    public function get_form_step_ids(){
      $form_steps = array();

      global $wpdb;
      $groups = $wpdb->get_results($wpdb->prepare("
        SELECT post_name
        FROM {$wpdb->prefix}posts
        WHERE post_type = %s
          AND post_content LIKE '%%%s%%'
          AND post_excerpt NOT LIKE 'defendant-id'
          AND post_status = 'publish'
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
        if($this->is_valid_form_post_type($form_type)){
          return $form_type;
        }
      }

      //$this->send_to_dashboard();
      return false;
    }

    public function validate_form(){
      if($this->skip_validation()){
        acf_reset_validation_errors();
      }
    }

    private function skip_validation(){
      //if(isset($_POST['direction']) && $_POST['direction'] == 'finish'){
      //  return false;
      //}

      if(isset($_GET['finished']) && $_GET['finished'] == 1){
        if((isset($_POST['direction'])) && ($_POST['direction'] == 'previous' || $_POST['direction'] == 'saveforlater')){
          return true;
        }

        return false;
      }

      return true;
    }

    private function is_valid_form_post_type($form_type){
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

    private function can_continue_requested_worksheet(){
      if(!$this->is_valid_form_post_type($this->form_post_type)){ return false; }

      if($this->form_post_id != 'new_post'){
        if(!$this->requested_post_is_valid()){
          return false;
        }
      }

      return true;
    }

    private function requested_post_is_valid(){
      //return (get_post_type((int)$_GET['post_id']) === $this->form_post_type && get_post_status((int)$_GET['post_id']) === 'publish');
      if(isset($_GET['post_id']) && $this->is_valid_token()){
        //does the requested post_id match the requested post_type
        if(get_post_type((int)$_GET['post_id']) !== $this->form_post_type){
          return false;
        }

        if(!$this->worksheet_belongs_to_user()){
          return false;
        }

        return true;
      }

      return false;
    }

    private function worksheet_belongs_to_user(){
      $worksheet_author = get_post_field('post_author', $this->form_post_id);
      if($worksheet_author == $this->user_id){
        return true;
      }

      return false;
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
      //wp_safe_redirect(home_url('dashboard'));
      //exit();
      echo '<p>' . sprintf(wp_kses_post(__('There was an error retrieving your form.  Please <a href="%s">return to your dashboard</a> and try again', 'pprsus'), array('a' => array('href' => array()))), esc_url(home_url('dashboard'))) . '</p>';
    }

    private function current_worksheet_is_finished(){
      return (isset($_GET['finished']) && (int)$_GET['finished'] === 1);
    }

    public function populate_date_field($value, $post_id, $field){
      if($value !== null){
        return $value;
      }

      return date('F j, Y');
    }
  }
}