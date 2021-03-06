<?php
/**
 * Review PSR Information
 */
if(!defined('ABSPATH')){ exit; }

if(!class_exists('PPRSUS_Review_Information')){
  class PPRSUS_Review_Information{
    private $defendant_info;
    private $user_id;

    public function __construct(){
      $this->defendant_info = $this->get_defendant_info();
      $this->user_id = get_current_user_id();

      //add_action('init', array($this, 'output_review_information'));
      $this->output_review_information();
    }

    public function output_review_information(){
      $this->output_information('defendants', $this->defendant_info['defendant_id']);

      $medical_id = $this->get_form_id('medical_history');
      if($medical_id){
        $this->output_information('medical_history', $medical_id);
      }
      else{
        $this->output_not_started('medical_history');
      }

      $security_id = $this->get_form_id('security');
      if($security_id){
        $this->output_information('security', $security_id);
      }
      else{
        $this->output_not_started('security');
      }
    }

    private function output_information($post_type, $post_id){
      $info_groups = $this->get_info_groups($post_type);
      $worksheet_link_counter = 1;

      foreach($info_groups as $group){
        $section_name = $this->get_section_name($group);
        $hidden_print = ($group == 'group_5d55924febe28') ? ' class="hidden-print"' : '';
        echo '<div class="info-section">
                <h3' . $hidden_print . '>' . esc_html__($section_name) . '</h3>
                <div class="info-group">';

        $fields = acf_get_fields($group);
        foreach($fields as $field){
          //print_var($field);
          if($this->conditional_met($field, $post_id) && $field['type'] != 'message'){
            if(have_rows($field['name'], $post_id) && $field['type'] != 'checkbox' && $field['type'] != 'select'){
                echo '<h3>' . esc_html($field['label']) . '</h3>';
              while(have_rows($field['name'], $post_id)){
                the_row();

                $field_row = get_row();
                foreach($field_row as $field_row_key => $field_row_value){
                  $field_row_object = get_sub_field_object($field_row_key, $post_id);

                  $this->output_field($field_row_object, $post_id);
                }
              }
            }
            else{
              $this->output_field($field, $post_id);
            }
          }
        }
        echo '</div>';
        $this->edit_worksheet_button($post_type, $post_id, $worksheet_link_counter);
        echo '</div>';

        $worksheet_link_counter++;
      }
    }

    private function edit_worksheet_button($post_type, $post_id, $worksheet_link_counter){
      $worksheet_link_args = array(
        'post_id' => $post_id,
        'defendant_id' => $this->defendant_info['defendant_id'],
        'step' => $worksheet_link_counter,
        'token' => get_post_meta($post_id, 'secret_token', true),
        'form_type' => $post_type
      );
      $worksheet_link = add_query_arg($worksheet_link_args, home_url('worksheet'));

      echo '<a href="' . $worksheet_link . '" class="button-primary hidden-print">' . esc_html__('Edit Worksheet') . '</a>';
    }

    private function output_field($field, $post_id){
      $field_label = $field['label'];
      $field_value = get_field($field['name'], $post_id);
      $field_width = $field['wrapper']['width'] ? $field['wrapper']['width'] : '100';

      if($field['type'] == 'true_false'){
        $field_value = $field['value'] == 1 ? 'Yes' : 'No';
      }

      if($field['type'] == 'checkbox' || $field['type'] == 'select'){
        //print_var($field);
        if(is_array($field_value)){
          //$field_value = implode(', ', $field['value']);
          $field_value = implode(', ', $field_value);
        }
      }

      //if($field['type'] == 'radio'){
        //print_var($field);
        //$field_value = $field['value'];
      //}

      if($field['type'] == 'radio' && is_array($field_value)){
        //$field_value = $field['value'];
        //$field_value = $field_value['value'];
        $field_value = $field['choices'][$field_value['value']];
      }

      if($field['type'] == 'post_object'){
        //print_var($field);
        if($field['value']){
          $field_value = $field['value']->post_title;
        }
      }

      $field_classes = array();
      $field_classes[] = 'info-field';
      if(($field['wrapper']['class'] == 'hide-print-if-no') && ($field_value == 'No' || $field_value == '' || $field_value == 'missing')){
        $field_classes[] = 'hide-print-if-no';
      }
      $field_class = implode(' ', $field_classes);

      echo '<div class="' . $field_class . '" style="width:' . $field_width . '%;">';
      echo '<strong>' . $field_label . '</strong><br />';
      if($field['required'] == 1 && ($field_value == null || $field_value == '')){
        echo '<span class="required hide-on-print">No entry - Required Field</span>';
      }
      else{
        if($field['wrapper']['class'] == 'disabled'){
          echo $field_value ? $field_value : '<span class="hide-on-print" style="font-style:italic;">To be entered after printing</span>';
        }
        else{
          echo $field_value ? $field_value : '<span class="hide-on-print" style="font-style:italic;">missing</span>';
        }
      }
      echo '</div>';
    }

    private function conditional_met($field, $post_id){
      $conditional_logic = $field['conditional_logic'];
      if(!is_array($conditional_logic)){ return true; }

      for($i = 0; $i < count($conditional_logic); $i++){
        $conditions = $conditional_logic[$i];
        for($ii = 0; $ii < count($conditions); $ii++){
          $conditional_field_object = get_field_object($conditions[$ii]['field'], $post_id);
          $conditional_field_value = $conditional_field_object['value'];

          $conditional_operator = $conditions[$ii]['operator'];
          $conditional_value = $conditions[$ii]['value'];

          switch($conditional_operator){
            case '!=':
              if($conditional_field_value == $conditional_value){
                return false;
              }
            break;

            case '==':
              if($conditional_field_value != $conditional_value){
                return false;
              }
            break;
          }
        }
      }

      return true;
    }

    private function output_not_started($post_type){
      $post_type_object = get_post_type_object($post_type);
      $post_type_name = $post_type_object->labels->singular_name;

      $get_started_link_args = array(
        'form_type' => $post_type,
        'defendant_id' => $this->defendant_info['defendant_id']
      );
      $get_started_link = add_query_arg($get_started_link_args, home_url('worksheet'));

      echo '<div class="info-section"><h3>' . sprintf(esc_html__('A %1$s profile has not been started.', 'pprsus'), $post_type_name) . '</h3>';
      echo sprintf('<a href="%1$s" class="button-primary hidden-print">%2$s</a>', $get_started_link, esc_html__('Get Started', 'pprsus'));
      echo '</div>';
    }

    private function get_form_id($post_type){
      $form_query_args = array(
        'post_type' => $post_type,
        'author' => $this->user_id,
        'posts_per_page' => 1,
        'meta_key' => 'defendant_id',
        'meta_value' => $this->defendant_info['defendant_id'],
        'post_status' => array('publish', 'draft')
      );

      $form_query = new WP_Query($form_query_args);
      if($form_query->have_posts()){
        while($form_query->have_posts()){
          $form_query->the_post();

          return get_the_ID();
        }
      }
      else{
        return false;
      }
    }

    private function get_section_name($group){
      global $wpdb;
      $group_name = $wpdb->get_var($wpdb->prepare("
        SELECT post_title
        FROM {$wpdb->prefix}posts
        WHERE post_name = %s", $group));

      $group_name_parts = explode('-', $group_name);

      return trim(end($group_name_parts));
    }

    private function get_info_groups($post_type){
      $info_groups = array();

      global $wpdb;
      $groups = $wpdb->get_results($wpdb->prepare("
        SELECT post_name
        FROM {$wpdb->prefix}posts
        WHERE post_type = %s
          AND post_content LIKE '%%%s%%'
          AND post_excerpt NOT LIKE 'defendant-id'
          AND post_status = 'publish'
          ORDER BY menu_order ASC", 'acf-field-group', $post_type));

      $g = 0;
      foreach($groups as $group){
        $info_groups[$g] = $group->post_name;
        $g++;
      }

      return $info_groups;
    }

    public function get_defendant_info(){
      if(isset($_GET['defendant_id']) && $_GET['defendant_id'] != ''){
        $defendant_id = sanitize_text_field($_GET['defendant_id']);
        $defendant_info['defendant_id'] = $defendant_id;
        $defendant_info['defendant_name'] = get_the_title($defendant_id);

        return $defendant_info;
      }
      else{
        $this->send_to_dashboard();
      }
    }

    private function send_to_dashboard(){
      wp_safe_redirect(esc_url(home_url('dashboard')));
      exit();
    }
  }
}