<?php
/**
 * Plugin Name: PPRS Prison Match
 * Description: PPRS Case Management Software: provides placement recommendations
 * Author: The Childress Agency
 * Author URI: https://childressagency.com
 * Version: 1.0
 * Text Domain: pprsus
 */
if(!defined('ABSPATH')){ exit; }

if(!defined('PPRSUS_PLUGIN_DIR')){
  define('PPRSUS_PLUGIN_DIR', dirname(__FILE__));
}
if(!defined('PPRSUS_PLUGIN_URL')){
  define('PPRSUS_PLUGIN_URL', plugin_dir_url(__FILE__));
}
if(!defined('PPRSUS_VERSION')){
  define('PPRSUS_VERSION', '1.0.0');
}

if(!class_exists('PPRSUS_Prison_Match')){
  class PPRSUS_Prison_Match{

    public function __construct(){
      $this->load_dependencies();
      $this->admin_init();
      $this->public_init();
      $this->define_template_hooks();
    }

    /**
     * Load Dependencies
     * 
     * include required files
     */
    public function load_dependencies(){
      require_once PPRSUS_PLUGIN_DIR . '/includes/pprsus-template-functions.php';
      require_once PPRSUS_PLUGIN_DIR . '/admin/class-pprsus-admin.php';
      require_once PPRSUS_PLUGIN_DIR . '/admin/class-pprsus-post-types.php';
      require_once PPRSUS_PLUGIN_DIR . '/public/class-pprsus-public.php';
      require_once PPRSUS_PLUGIN_DIR . '/includes/class-pprsus-multistep-worksheet.php';
      require_once PPRSUS_PLUGIN_DIR . '/includes/class-pprsus-import-bop-drugs.php';
      require_once PPRSUS_PLUGIN_DIR . '/includes/class-pprsus-import-prison-data.php';
    }//end load_dependencies

    /**
     * Admin Init
     * 
     * load admin required hooks
     */
    public function admin_init(){
      $pprsus_admin = new PPRSUS_Admin();

      /**
       * uncomment the next 2 lines below to enable the cron job.
       * The cron job will first check for and delete any Defendant's Personal Information, Medical History
       * and Security Information that's older than the number of months set in "Prison Match Form Settings" -> 
       * "Max Defendant Record Age".  Then it will look for Defendants older than the number of months set in
       * "Prison Match Form Settings" -> "Defendant Record Send Email", and send out the warning email.
       */
      //add_action('wp', array($pprsus_admin, 'create_cron_job'))
      //add_action('check_expiring_defendants', array($pprsus_admin, 'check_expiring_defendants'));

      if(!class_exists('acf')){
        require_once PPRSUS_PLUGIN_DIR . '/vendors/advanced-custom-fields-pro/acf.php';
        add_filter('acf/settings/path', array($pprsus_admin, 'acf_settings_path'));
        add_filter('acf/settings/dir', array($pprsus_admin, 'acf_settings_dir'));
      }

      add_action('init', array($this, 'load_textdomain'));

      add_action('acf/init', array($pprsus_admin, 'add_acf_options_page'));

      $pprsus_post_types = new PPRSUS_Post_Types();
      add_action('init', array($pprsus_post_types, 'create_post_types'));
    }//end admin_init

    /**
     * define hooks for public facing stuff
     */
    public function public_init(){
      $pprsus_public = new PPRSUS_Public();

      add_action('wp_enqueue_scripts', array($pprsus_public, 'enqueue_scripts'));
      add_action('wp_enqueue_scripts', array($pprsus_public, 'enqueue_styles'));

      add_action('wp_ajax_nopriv_pprsus_change_user', array($pprsus_public, 'pprsus_change_user'));
      add_action('wp_ajax_pprsus_change_user', array($pprsus_public, 'pprsus_change_user'));

      $worksheet = new PPRSUS_MultiStep_Worksheet();
      add_action('acf/validate_save_post', array($worksheet, 'validate_form'), 10, 0);
      add_action('acf/save_post', array($worksheet, 'process_acf_form'), 20);

      //auto populate date field on Defendant Personal Information
      add_filter('acf/load_value/key=field_5ce6b5cad633f', array($worksheet, 'populate_date_field'), 20, 3);

      new PPRSUS_Import_BOP_Drugs();
      new PPRSUS_Import_Prison_Data();

      add_filter('acf/fields/post_object/result/key=field_5d28e0996781d', array($this, 'add_term_to_medication'), 10, 4);
    }

    public function add_term_to_medication($title, $post, $field, $post_id){
      $post_terms = wp_get_post_terms($post->ID, 'drug_types', array('fields' => 'names'));
      $title .= ' (' . $post_terms[0] . ')';

      return $title;
    }

    /**
     * define template hooks
     * register general template hooks
     */
    public function define_template_hooks(){
      $template_functions = new PPRSUS_Template_Functions();

      add_filter('template_include', array($template_functions, 'load_template'), 99);

      add_action('pprsus_to_dashboard_btn', array($template_functions, 'to_dashboard_btn'));
      add_action('pprsus_before_worksheet', array($template_functions, 'before_worksheet'));
      add_action('pprsus_worksheet', array($template_functions, 'worksheet'));

      add_action('pprsus_before_dashboard', array($template_functions, 'before_dashboard'));
      add_action('pprsus_dashboard', array($template_functions, 'dashboard'));

      add_action('pprsus_before_review_information', array($template_functions, 'before_review_information'));
      add_action('pprsus_review_information', array($template_functions, 'review_information'));
      add_action('pprsus_after_review_information', array($template_functions, 'after_review_information'));

      add_action('pprsus_before_match_prisons', array($template_functions, 'before_match_prisons'));
      add_action('pprsus_match_prisons', array($template_functions, 'match_prisons'));
    }//end define_template_hooks

    public function load_textdomain(){
      load_plugin_textdomain('pprsus', false, basename(dirname(__FILE__)) . '/languages');
    }//end load_textdomain
  }//end class
}

new PPRSUS_Prison_Match();