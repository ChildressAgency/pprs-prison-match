<?php
/**
 * globally accessible functions
 */
if(!defined('ABSPATH')){ exit; }

function print_var($var){
  return (new PPRSUS_Template_Functions)->var_print($var);
}

function pprsus_get_template($template_name){
  return (new PPRSUS_Template_Functions)->find_template($template_name);
}

function get_list_of_prisons($prison_security_level_term_ids, $prison_care_level_term_id, $lat_lng, $distance, $orderby = 'title'){
  return (new PPRSUS_Template_Functions)->get_prison_list($prison_security_level_term_ids, $prison_care_level_term_id, $lat_lng, $distance, $orderby);
}

if(!class_exists('PPRSUS_Template_Functions')){
  class PPRSUS_Template_Functions{

    public function worksheet(){
      require_once PPRSUS_PLUGIN_DIR . '/includes/class-pprsus-multistep-worksheet.php';
      $worksheet = new PPRSUS_MultiStep_Worksheet(); 
      $worksheet->output_acf_form();
      //$worksheet->init();
    }

    public function dashboard(){
      require_once PPRSUS_PLUGIN_DIR . '/includes/class-pprsus-dashboard.php';
      new PPRSUS_Dashboard();
    }

    public function review_information(){
      require_once PPRSUS_PLUGIN_DIR . '/includes/class-pprsus-review-information.php';
      new PPRSUS_Review_Information();
    }

    public function match_prisons(){
      require_once PPRSUS_PLUGIN_DIR . '/includes/class-pprsus-match-prisons.php';
      new PPRSUS_Match_Prisons();
    }

    public function to_dashboard_btn(){
      echo '<div class="btn-wrapper hidden-print">';
      echo  '<a href="' . esc_url(home_url('dashboard')) . '" class="btn">' . esc_html__('&lt; Back to Dashboard', 'pprsus') . '</a>';
      echo '</div>';
    }

    public function before_worksheet(){
      $before_worksheet_content = get_option('options_before_worksheet_content');

      if($before_worksheet_content){
        echo apply_filters('the_content', wp_kses_post($before_worksheet_content));
      }
    }

    public function before_dashboard(){
      echo '<div class="dashboard-header"><div class="btn_wrapper">';
        $new_defendant_link = add_query_arg(array('form_type' => 'defendants'), home_url('worksheet'));
        echo sprintf('<a href="%1$s" class="btn">%2$s</a>', esc_url($new_defendant_link), esc_html__('+ Create New Profile', 'pprsus'));
      echo '</div></div>';
    }

    public function before_review_information(){
      echo '<h2>' . esc_html__('Review PSR Information', 'pprsus') . '</h2>';
    }

    public function before_match_prisons(){
      $before_match_prisons_content = get_option('options_before_match_prisons_content');

      if($before_match_prisons_content){
        echo apply_filters('the_content', wp_kses_post($before_match_prisons_content));
      }
    }

    public function load_template($template){
      $template_name = '';

      if(is_page('dashboard')){
        $template_name = 'page-dashboard.php';
      }
      elseif(is_page('worksheet')){
        $template_name = 'page-worksheet.php';
      }
      elseif(is_page('review-information')){
        $template_name = 'page-review-information.php';
      }
      elseif(is_page('mental-health-addendum')){
        $template_name = 'page-mental-health-addendum.php';
      }
      elseif(is_page('match-prisons')){
        $template_name = 'page-match-prisons.php';
      }
      elseif(is_singular('prison_data')){
        $template_name = 'single-prison_data.php';
      }

      if($template_name !== ''){
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

    public function var_print($var){
      //echo '<pre>' . print_r($var, true) . '</pre>';
      echo '<pre>';
      var_dump($var);
      echo '</pre>';
    }

    public function get_prison_list($prison_security_level_term_ids, $prison_care_level_term_id, $lat_lng, $distance, $orderby){
      if($orderby == 'title'){
        $order_by = 'posts.post_title';
      }
      else{
        $order_by = 'distance';
      }

      global $wpdb;
      $prison_list = $wpdb->get_results($wpdb->prepare("
        SELECT DISTINCT posts.ID,
          ((ACOS(SIN(%f * PI() / 180) * SIN(prison_lat.meta_value * PI() / 180) + COS(%f * PI() / 180) * COS(prison_lat.meta_value * PI() / 180) * COS((%f - prison_lng.meta_value) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance
        FROM {$wpdb->prefix}postmeta AS prison_lat
        INNER JOIN {$wpdb->prefix}posts AS posts ON posts.ID = prison_lat.post_id
        LEFT JOIN {$wpdb->prefix}postmeta AS prison_lng ON prison_lat.post_id = prison_lng.post_id
        LEFT JOIN {$wpdb->prefix}term_relationships AS relationships ON posts.ID = relationships.object_id
        LEFT JOIN {$wpdb->prefix}term_relationships AS tt1 ON posts.ID = tt1.object_id
        WHERE posts.post_type = 'prison_data'
          AND posts.post_status = 'publish'
          AND relationships.term_taxonomy_id IN (" . join(',', array_map('esc_sql', $prison_security_level_term_ids)) . ")
          AND tt1.term_taxonomy_id IN (%d)
          AND prison_lat.meta_key = 'latitude'
          AND prison_lng.meta_key = 'longitude'
        GROUP BY posts.ID
        HAVING distance < %d
        ORDER BY " . $order_by . " ASC",
        $lat_lng['lat'],
        $lat_lng['lat'],
        $lat_lng['lng'],
        $prison_care_level_term_id,
        $distance
      ));

      return $prison_list;
    }
  }//end class
}