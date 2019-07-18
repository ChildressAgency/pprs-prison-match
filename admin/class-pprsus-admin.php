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

    public function create_cron_job(){
      if(!wp_next_scheduled('check_expiring_defendants')){
        wp_schedule_event(current_time('timestamp'), 'daily', 'check_expiring_defendants');
      }
    }

    public function check_expiring_defendants(){
      //first use wp_query to get defendant posts using date_query with column => post_modified_gmt
      //then using those ids use custom meta query with defendant id to find matching medical and security forms
      //also using the date_query.  If none are found in the meta_query then while the defendant wasn't modified in the 
      //6 months, one of the other forms must have been.

      //then remove any defendant, medical, security posts older than 7 months.

      $max_age = get_field('max_defendant_record_age', 'option');
      $max_age_email = get_field('defendant_record_send_email', 'option');

      $delete_defendants = $this->delete_old_defendants($max_age);

      //echo $delete_defendants;

      $email_reminder = $this->email_deletion_reminder($max_age_email);
    }

    private function email_deletion_reminder($max_age_email){
      $month = date('Ymd', strtotime('first day of -' . $max_age_email . ' month'));

      $expiring_emails = new WP_Query(array(
        'post_type' => 'defendants',
        'posts_per_page' => -1,
        'post_status' => array('draft', 'publish'),
        'meta_query' => array(
          array(
            'key' => 'last_modified_date',
            'compare' => '<',
            'value' => $month
          )
        )
      ));

      if($expiring_emails->have_posts()){
        while($expiring_emails->have_posts()){
          $expiring_emails->the_post();

          $email_reminder = $this->send_email_reminder(get_the_ID());
        }
      }
    }

    private function send_email_reminder($defendant_id){
      $author_id = get_post_field('post_author', $defendant_id);
      $to = get_the_author_meta('user_email', $author_id);
      $cc = get_field('defendant_expiration_cc_emails', 'option');
      $cc = explode(',', $cc);
      $from = get_field('defendant_expiration_email_from_field', 'option');

      $subject = get_field('defendant_expiration_subject', 'option');

      $headers = array('Content-Type: text/html; charset=UTF-8');
      foreach($cc as $c){
        $headers[] = 'Cc: ' . $c;
      }
      $headers[] = 'From: ' . $from;

      $defendant_name = get_the_title($defendant_id);
      $author_name = get_the_author_meta('display_name', $author_id);

      $message = get_field('defendant_expiration_message', 'option');
      $message = str_replace('%%defendant_name%%', $defendant_name, $message);
      $message = str_replace('%%user_name%%', $author_name, $message);

      return wp_mail($to, $subject, $message, $headers);
    }

    private function delete_old_defendants($max_age){
      $month = date('Ymd', strtotime('first day of -' . $max_age . ' month'));
      
      $old_defendants = new WP_Query(array(
        'post_type' => 'defendants',
        'posts_per_page' => -1,
        'post_status' => array('draft', 'publish'),
        'meta_query' => array(
          array(
            'key' => 'last_modified_date',
            'compare' => '<',
            'value' => $month
          )
        )
      ));

      if($old_defendants->have_posts()){
        while($old_defendants->have_posts()){
          $old_defendants->the_post();
          $defendant_id = get_the_ID();

          //delete security info
          $this->delete_defendant_info($defendant_id, 'security');
          //delete medical history
          $this->delete_defendant_info($defendant_id, 'medical_history');
          //delete the defendant
          wp_delete_post($defendant_id, true);

          echo esc_html__('Success', 'pprsus');
        }
      }
      else{
        return esc_html__('No old defendants found.', 'pprsus');
      }
      wp_reset_postdata();
    }

    private function delete_defendant_info($defendant_id, $post_type){
      $post_to_delete = new WP_Query(array(
        'post_type' => $post_type,
        'post_status' => array('draft', 'publish'),
        'posts_per_page' => 1,
        'meta_key' => 'defendant_id',
        'meta_value' => $defendant_id
      ));

      if($post_to_delete->have_posts()){
        while($post_to_delete->have_posts()){
          $post_to_delete->the_post();

          wp_delete_post(get_the_ID(), true);
        }
      }
      wp_reset_postdata();
    }
  }//end class
}