<?php
/**
 * admin specific functions
 */
if(!defined('ABSPATH')){ exit; }

if(!class_exists('PPRSUS_Admin')){
  class PPRSUS_Admin{
    public function __construct(){
      //$this->load_dependencies();
      add_filter('acf/load_field/key=field_5d25e958f2d66', array($this, 'load_question_choices'));
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

      acf_add_options_page(array(
        'page_title' => esc_html__('Care Level Questions', 'pprsus'),
        'menu_title' => esc_html__('Care Level Questions', 'pprsus'),
        'menu_slug' => 'care-level-questions',
        'capability' => 'edit_posts',
        'redirect' => false
      ));
    }

    /*public function add_care_level_setting($field){
      //adds the care level setting field to each acf field
      $care_level_choices = array(
        'zero' => 'Not Used',
        'one' => 'Care Level I',
        'two' => 'Care Level II',
        'three' => 'Care Level III',
        'four' => 'Care Level IV'
      );

      acf_render_field_setting($field, array(
        'label' => esc_html__('Care Level Setting', 'pprsus'),
        'instructions' => '',
        'name' => 'care_level',
        'type' => 'radio',
        'choices' => $care_level_choices,
        'default_value' => 'zero',
        'other_choice' => 0,
        'allow_null' => 0,
        'return_format' => 'value'
      ), true);
    }*/

    public function load_question_choices($field){
      $field['choices'] = array();
      $medical_forms_keys = $this->get_medical_forms_keys();

      foreach($medical_forms_keys as $medical_form_key){
        $form_fields = acf_get_fields($medical_form_key);

        foreach($form_fields as $form_field){
          if($form_field['type'] != 'message'){
            $field['choices'][$form_field['name']] = $form_field['label'];
          }
        }

      }

      return $field;
    }

    private function get_medical_forms_keys(){
      $form_keys = array();

      global $wpdb;
      $groups = $wpdb->get_results($wpdb->prepare("
        SELECT post_name
        FROM {$wpdb->prefix}posts
        WHERE post_type = %s
          AND post_content LIKE '%%%s%%'
          AND post_excerpt NOT LIKE 'defendant-id'
          AND post_status = 'publish'
          ORDER BY menu_order ASC", 'acf-field-group', 'medical_history'));
      
      $g = 0;
      foreach($groups as $group){
        $form_keys[$g] = $group->post_name;
        $g++;
      }

      return $form_keys;
    }
  }//end class
}