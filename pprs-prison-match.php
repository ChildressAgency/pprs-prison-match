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
    }//end load_dependencies

    /**
     * Admin Init
     * 
     * load admin required hooks
     */
    public function admin_init(){
      $pprsus_admin = new PPRSUS_Admin();

      if(!class_exists('acf')){
        require_once PPRSUS_PLUGIN_DIR . '/vendors/advanced-custom-fields-pro/acf.php';
        add_filter('acf/settings/path', array($pprsus_admin, 'acf_settings_path'));
        add_filter('acf/settings/dir', array($pprsus_admin, 'acf_settings_dir'));
      }

      add_action('init', array($this, 'load_textdomain'));

      $pprsus_post_types = new PPRSUS_Post_Types();
      add_action('init', array($pprsus_post_types, 'create_post_types'));
    }//end admin_init

    /**
     * define template hooks
     * register general template hooks
     */
    public function define_template_hooks(){
      $template_functions = new PPRSUS_Template_Functions();

      add_filter('template_include', array($template_functions, 'load_template'), 99);
    }//end define_template_hooks

    public function load_textdomain(){
      load_plugin_textdomain('pprsus', false, basename(dirname(__FILE__)) . '/languages');
    }//end load_textdomain
  }//end class
}

new PPRSUS_Prison_Match();