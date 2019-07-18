<?php
/**
 * create post types
 */
if(!defined('ABSPATH')){ exit; }

if(!class_exists('PPRSUS_Post_Types')){
  class PPRSUS_Post_Types{

    public function create_post_types(){
      $defendant_labels = array(
        'name' => esc_html_x('Defendants', 'post type general name', 'pprsus'),
        'singular_name' => esc_html_x('Defendant', 'post type singular name', 'pprsus'),
        'menu_name' => esc_html_x('Defendants', 'post type menu name', 'pprsus'),
        'add_new_item' => esc_html__('Add New Defendant', 'pprsus'),
        'search_items' => esc_html__('Search Defendants', 'pprsus'),
        'edit_item' => esc_html__('Edit Defendant', 'pprsus'),
        'view_item' => esc_html__('view Defendant', 'pprsus'),
        'all_items' => esc_html__('All Defendants', 'pprsus'),
        'new_item' => esc_html__('New Defendant', 'pprsus'),
        'not_found' => esc_html__('No Defendants Found', 'pprsus')
      );

      $defendant_args = array(
        'labels' => $defendant_labels,
        'capability_type' => 'post',
        'public' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-businessperson',
        'query_var' => 'defendant',
        'has_archive' => false,
        'show_in_rest' => true,
        'supports' => array(
          'title',
          'custom-fields',
          'revisions',
          'author'
        )
      );
      register_post_type('defendants', $defendant_args);

      $medical_history_labels = array(
        'name' => esc_html_x('Medical Histories', 'post type general name', 'pprsus'),
        'singular_name' => esc_html_x('Medical History', 'post type singular name' , 'pprsus'),
        'menu_name' => esc_html_x('Medical Histories', 'post type menu name' , 'pprsus'),
        'add_new_item' => esc_html__('Add New Medical History', 'pprsus'),
        'search_items' => esc_html__('Search Medical Histories', 'pprsus'),
        'edit_item' => esc_html__('Edit Medical History', 'pprsus'),
        'view_item' => esc_html__('View Medical History', 'pprsus'),
        'all_items' => esc_html__('All Medical Histories', 'pprsus'),
        'new_item' => esc_html__('New Medical History', 'pprsus'),
        'not_found' => esc_html__('Medical History Not Found', 'pprsus')
      );

      $medical_history_args = array(
        'labels' => $medical_history_labels,
        'capability_type' => 'post',
        'public' => true,
        'menu_position' => 6,
        'menu_icon' => 'dashicons-universal-access',
        'query_var' => 'medical_history',
        'has_archive' => false,
        'show_in_rest' => true,
        'supports' => array(
          'title',
          'custom-fields',
          'revisions',
          'author'
        )
      );
      register_post_type('medical_history', $medical_history_args); 
      
      $security_labels = array(
        'name' => esc_html_x('Security Profiles', 'post type general name' , 'pprsus'),
        'singular_name' => esc_html_x('Security Profile', 'post type singular name', 'pprsus'),
        'menu_name' => esc_html_x('Security Profiles', 'post type menu name', 'pprsus'),
        'add_new_item' => esc_html__('Add New Security Profile', 'pprsus'),
        'search_items' => esc_html__('Search Security Profiles', 'pprsus'),
        'edit_item' => esc_html__('Edit Security Profile', 'pprsus'),
        'view_item' => esc_html__('View Security Profile', 'pprsus'),
        'all_items' => esc_html__('All Security Profiles', 'pprsus'),
        'new_item' => esc_html__('New Security Profile', 'pprsus'),
        'not_found' => esc_html__('No security Profiles Found', 'pprsus')
      );

      $security_args = array(
        'labels' => $security_labels,
        'capability_type' => 'post',
        'public' => true,
        'menu_position' => 7,
        'menu_icon' => 'dashicons-shield-alt',
        'query_var' => 'security',
        'has_archive' => false,
        'show_in_rest' => true,
        'supports' => array(
          'title',
          'custom-fields',
          'revisions',
          'author'
        )
      );
      register_post_type('security', $security_args);

      $bop_drugs_labels = array(
        'name' => esc_html_x('BOP Drugs', 'post type general name', 'pprsus'),
        'singular_name' => esc_html_x('BOP Drug', 'post type singular name', 'pprsus'),
        'menu_name' => esc_html_x('BOP Drug List', 'post type menu name', 'pprsus'),
        'add_new_item' => esc_html__('Add New BOP Drug', 'pprsus'),
        'search_items' => esc_html__('Search BOP Drug List', 'pprsus'),
        'edit_item' => esc_html__('Edit BOP Drug', 'pprsus'),
        'view_item' => esc_html__('View BOP Drug', 'pprsus'),
        'all_items' => esc_html__('All BOP Drugs', 'pprsus'),
        'new_item' => esc_html__('New BOP Drug', 'pprsus'),
        'not_found' => esc_html__('BOP Drug Not Found', 'pprsus')
      );

      $bop_drugs_args = array(
        'labels' => $bop_drugs_labels,
        'capability_type' => 'post',
        'public' => true,
        'menu_position' => 8,
        'menu_icon' => 'dashicons-plus-alt',
        'query_var' => 'bop_drugs',
        'has_archive' => false,
        'show_in_rest' => true,
        'supports' => array(
          'title',
          'custom-fields',
          'revisions',
          'author'
        )
      );
      register_post_type('bop_drugs', $bop_drugs_args);

      register_taxonomy(
        'drug_types',
        'bop_drugs',
        array(
          'hierarchical' => true,
          'show_admin_column' => true,
          'public' => true,
          'labels' => array(
            'name' => esc_html_x('Drug Types', 'taxonomy general name', 'pprsus'),
            'singular_name' => esc_html_x('Drug Type', 'taxonomy singular name', 'pprsus'),
            'menu_name' => esc_html_x('Drug Types', 'taxonomy menu name', 'pprsus'),
            'search_items' => esc_html__('Search Drug Types', 'pprsus'),
            'all_items' => esc_html__('All Drug Types', 'pprsus'),
            'parent_item' => esc_html__('Parent Drug Type', 'pprsus'),
            'parent_item_colon' => esc_html__('Parent Drug Type:', 'pprsus'),
            'edit_item' => esc_html__('Edit Drug Type', 'pprsus'),
            'update_item' => esc_html__('Update Drug Type', 'pprsus'),
            'add_new_item' => esc_html__('Add New Drug Type', 'pprsus'),
            'new_item_name' => esc_html__('New Drug Type Name', 'pprsus')
          )
        )
      );

      $prison_data_labels = array(
        'name' => esc_html_x('Prison Data', 'post type general name', 'pprsus'),
        'singular_name' => esc_html_x('Prison Data', 'post type singular name', 'pprsus'),
        'menu_name' => esc_html_x('Prison Data', 'post type menu name', 'pprsus'),
        'add_new_item' => esc_html__('Add New Prison Data', 'pprsus'),
        'search_items' => esc_html__('Search Prison Data', 'pprsus'),
        'edit_item' => esc_html__('Edit Prison Data', 'pprsus'),
        'view_item' => esc_html__('View Prison Data', 'pprsus'),
        'add_items' => esc_html__('All Prison Data', 'pprsus'),
        'new_item' => esc_html__('New Prison Data', 'pprsus'),
        'not_found' => esc_html__('Prison Data Not Found', 'pprsus')
      );

      $prison_data_args = array(
        'labels' => $prison_data_labels,
        'capability_type' => 'post',
        'public' => true,
        'menu_position' => 6,
        'menu_icon' => 'dashicons-analytics',
        'query_var' => 'prison_data',
        'has_archive' => false,
        'show_in_rest' => true,
        'supports' => array(
          'title',
          'editor',
          'custom_fields',
          'revisions',
          'author'
        )
      );
      register_post_type('prison_data', $prison_data_args);

      register_taxonomy(
        'security_level',
        'prison_data',
        array(
          'hierarchical' => true,
          'show_admin_column' => true,
          'public' => true,
          'labels' => array(
            'name' => esc_html_x('Security Level', 'taxonomy general name', 'pprsus'),
            'singular_name' => esc_html_x('Security Level', 'taxonomy singular name', 'pprsus'),
            'menu_name' => esc_html_x('Security Levels', 'taxonomy menu name', 'pprsus'),
            'search_items' => esc_html__('Search Security Levels', 'pprsus'),
            'all_items' => esc_html__('All Security Levels', 'pprsus'),
            'parent_item' => esc_html__('Parent Security Level', 'pprsus'),
            'parent_item_colon' => esc_html__('Parent Security Level:', 'pprsus'),
            'edit_item' => esc_html__('Edit Security Level', 'pprsus'),
            'update_item' => esc_html__('Update Security Level', 'pprsus'),
            'add_new_item' => esc_html__('Add New Security Level', 'pprsus'),
            'new_item_name' => esc_html__('New Security Level', 'pprsus')
          )
        )
      );

      register_taxonomy(
        'facility_care_level',
        'prison_data',
        array(
          'hierarchical' => true,
          'show_admin_column' => true,
          'public' => true,
          'labels' => array(
            'name' => esc_html_x('Facility Care Levels', 'taxonomy general name', 'pprsus'),
            'singular_name' => esc_html_x('Facility Care Level', 'taxonomy singular name', 'pprsus'),
            'menu_name' => esc_html_x('Facility Care Levels', 'taxonomy menu name', 'pprsus'),
            'search_items' => esc_html__('Search Facility Care Levels', 'pprsus'),
            'all_items' => esc_html__('All Facility Care Levels', 'pprsus'),
            'parent_item' => esc_html__('Parent Facility Care Level' , 'pprsus'),
            'parent_item_colon' => esc_html__('Parent Facility Care Level:', 'pprsus'),
            'edit_item' => esc_html__('Edit Facility Care Levels', 'pprsus'),
            'update_item' => esc_html__('Update Facility Care Levels', 'pprsus'),
            'add_new_item' => esc_html__('Add New Facility Care Level', 'pprsus'),
            'new_item_name' => esc_html__('New Facility Care Level', 'pprsus')
          )
        )
      );
    }//end create_post_types()
  }
}