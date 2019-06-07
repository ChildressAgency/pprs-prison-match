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

    public function worksheet(){
      require_once PPRSUS_PLUGIN_DIR . '/includes/class-pprsus-multistep-worksheet.php';
      new PPRSUS_MultiStep_Worksheet();
    }

    public function dashboard(){
      require_once PPRSUS_PLUGIN_DIR . '/includes/class=pprsus-dashboard.php';
      new PPRSUS_Dashboard();
    }

    public function review_information(){
      require_once PPRSUS_PLUGIN_DIR . '/includes/class-pprsus-review-information.php';
      new PPRSUS_Review_Information();
    }

    public function to_dashboard_btn(){
      echo '<div class="btn-wrapper">';
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
        echo sprintf('<a href="%1$s" class="btn">%2$s</a>', esc_url($new_defendant_link), esc_html__('&plus; Create New Profile', 'pprsus'));
      echo '</div></div>';
    }

    public function before_review_information(){
      echo '<h2>' . esc_html__('Review PSR Information', 'pprsus') . '</h2>';
    }

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