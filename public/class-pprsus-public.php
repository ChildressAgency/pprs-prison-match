<?php
/**
 * non admin functions
 */
if(!defined('ABSPATH')){ exit; }

if(!class_exists('PPRSUS_Public')){
  class PPRSUS_Public{

    /**
     * enqueue scripts
     */
    public function enqueue_scripts(){
      wp_register_script(
        'pprsus-script',
        PPRSUS_PLUGIN_URL . 'js/pprsus-scripts.js',
        array('jquery'),
        PPRSUS_VERSION,
        true
      );

      wp_register_script(
        'tablefilter',
        PPRSUS_PLUGIN_URL . 'vendors/tablefilter/tablefilter.js',
        array('jquery'),
        PPRSUS_VERSION,
        true
      );

      wp_enqueue_script('tablefilter');
      wp_enqueue_script('pprsus-script');

      wp_localize_script('tablefilter', 'tablefilter_settings', array(
        'tablefilter_basepath' => PPRSUS_PLUGIN_URL . 'vendors/tablefilter/',
        'tablefilter_clear_text' => esc_html__('Display All', 'pprsus')
      ));
    }

    /**
     * enqueue styles
     */
    public function enqueue_styles(){
      wp_register_style(
        'pprsus-style',
        PPRSUS_PLUGIN_URL . 'css/pprsus-style.css',
      );

      wp_register_style(
        'tablefilter-style',
        PPRSUS_PLUGIN_URL . 'vendors/tablefilter/style/tablefilter.css'
      );

      wp_enqueue_style('tablefilter-style');
      wp_enqueue_style('pprsus-style');
    }
  }
}