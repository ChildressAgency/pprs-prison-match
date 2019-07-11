<?php
/**
 * Match the Prisons using the Security score information
 */
if(!defined('ABSPATH')){ exit; }

if(!class_exists('PPRSUS_Match_Prisons')){
  class PPRSUS_Match_Prisons{

    private $user_id;
    private $security_form_id;
    private $defendant_id;
    private $token;
    private $error_message = '';

    public function __construct(){
      $this->user_id = get_current_user_id();
      $this->security_form_id = $this->get_query_param('post_id');
      $this->defendant_id = $this->get_query_param('defendant_id');
      $this->token = $this->get_query_param('token');
      
      $this->output_prison_list();
    }

    public function output_prison_list(){
      if(!$this->allowed_to_view_matches()){
        echo $this->error_message;
        return;
      }

      $security_score = $this->get_security_score();
      //prison_security_level = array of term ids
      $prison_security_level = $this->get_prison_security_level($security_score);
      //$prison_security_level_term_ids = implode(',', $prison_security_level);
      $prison_security_level_term_ids = array_map('intval', $prison_security_level);

      //prison care level = term slug
      $prison_care_level_number = $this->get_prison_care_level();
      $prison_care_level = $this->convert_numbers_to_word($prison_care_level_number);
      $prison_care_level_term = get_term_by('slug', $prison_care_level, 'facility_care_level');
      $prison_care_level_term_id = $prison_care_level_term->term_id;

      $lat_lng = $this->get_defendant_lat_lng();

      $distance = get_field('show_prisons_within', 'option');

      //print_var($lat_lng);
      
      /*$prison_list = new WP_Query(array(
        'post_type' => 'prison_data',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'tax_query' => array(
          'relation' => 'AND',
          array(
            'taxonomy' => 'security_level',
            'field' => 'term_id',
            'terms' => $prison_security_level
          ),
          array(
            'taxonomy' => 'facility_care_level',
            'field' => 'slug',
            'terms' => $prison_care_level
          )
        )
      ));*/

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
        ORDER BY posts.post_title ASC",
        $lat_lng['lat'],
        $lat_lng['lat'],
        $lat_lng['lng'],
        $prison_care_level_term_id,
        $distance
      ));

      if($prison_list){
        include pprsus_get_template('loop/matched-prisons.php');
      }
      else{
        echo apply_filters('the_content', wp_kses_post(get_field('no_prisons_matched_message', 'option')));
      }
    }

    private function get_defendant_lat_lng(){
      $lat_lng = array();
      $zipcode = get_field('zip_code', $this->defendant_id);
      $api_key = get_field('google_maps_api_key', 'option');
      $geocode_url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $zipcode . '&key=' . $api_key;

      //$api_response = file_get_contents($geocode_url);
      $api_response = $this->get_api_response($geocode_url);
      $location_info = json_decode($api_response, true);

      if($location_info['status'] == 'OK'){
        $lat = isset($location_info['results'][0]['geometry']['location']['lat']) ? $location_info['results'][0]['geometry']['location']['lat'] : '';
        $lng = isset($location_info['results'][0]['geometry']['location']['lng']) ? $location_info['results'][0]['geometry']['location']['lng'] : '';

        $lat_lng['lat'] = $lat;
        $lat_lng['lng'] = $lng;

        return $lat_lng;
      }
      else{
        return '<p>error: ' . $location_info['status'] . '</p>';
      }
    }

    private function get_api_response($geocode_url){
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $geocode_url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $response = curl_exec($ch);
      curl_close($ch);

      return $response;
    }

    private function convert_numbers_to_word($number){
      $number = str_replace('1', 'one', $number);
      $number = str_replace('2', 'two', $number);
      $number = str_replace('3', 'three', $number);
      $number = str_replace('4', 'four', $number);

      return $number;
    }

    private function get_prison_care_level(){
      $care_level = 0;
      if(have_rows('care_level_question', 'option')){
        while(have_rows('care_level_question', 'option')){
          the_row();

          $question = get_sub_field('question');
          $question_care_level = get_sub_field('care_level');

          $medical_form_ids = $this->get_medical_form_ids();

          foreach($medical_form_ids as $form_id){
            $medical_question = get_field($question['value'], $form_id);

            if(($medical_question) && ($medical_question == 'yes' || $medical_question == true)){

              if((int)$care_level < (int)$question_care_level['value']){
                $care_level = $question_care_level['value'];
              }
            }
          }
        }
      }

      return $care_level;
    }

    private function get_medical_form_ids(){
      $medical_form_ids = array();

      $medical_posts_for_defendant = new WP_Query(array(
        'post_type' => 'medical_history',
        'posts_per_page' => 1,
        'post_status' => array('draft', 'publish'),
        'meta_key' => 'defendant_id',
        'meta_value' => $this->defendant_id
      ));

      if($medical_posts_for_defendant->have_posts()){
        while($medical_posts_for_defendant->have_posts()){
          $medical_posts_for_defendant->the_post();

          $medical_form_ids[] = get_the_ID();
        }
      }

      return $medical_form_ids;
    }

    private function get_prison_security_level($security_score){
      $gender = strtolower(get_field('sex', $this->defendant_id));
      $minimum = get_field('minimum', 'option');
      $low = get_field('low', 'option');
      $medium = get_field('medium', 'option');
      $high = get_field('high', 'option');

      if($security_score <= $minimum[$gender . '_high_score']){
        return $minimum['prison_security_levels'];
      }
      elseif($security_score <= $low[$gender . '_high_score']){
        return $low['prison_security_levels'];
      }
      elseif($security_score <= $medium[$gender . '_high_score']){
        return $medium['prison_security_levels'];
      }
      else{
        return $high['prison_security_levels'];
      }
    }

    private function get_security_score(){
      $security_form_fields = get_fields($this->security_form_id, false);
      $security_score = 0;

      foreach($security_form_fields as $key => $value){
        if($value == 'unknown'){ $value = 0; }
        $security_score = $security_score + (int)$value;
      }

      return $security_score;
    }

    private function allowed_to_view_matches(){
      //does the user have membership access
      if(!rcp_user_can_access($this->user_id, $this->security_form_id) || !rcp_user_can_access($this->user_id, $this->defendant_id)){
        $this->error_message = esc_html__('You do not have access to this report.', 'pprsus');
        return false;
      }
      //are the variables even set
      if($this->security_form_id == '' || $this->defendant_id == '' || $this->token == ''){
        $this->error_message = esc_html__('There was a problem retrieving the prison list. Please return to the dashboard and try again.', 'pprsus');
        return false;
      }

      //is this a security form post type
      if(get_post_type($this->security_form_id) != 'security'){
        $this->error_message = esc_html__('There was a problem retrieving the prison list. Please return to the dashboard and try again.', 'pprsus');
        return false;
      }

      //does the security form or defendant belong to the current user
      $security_form_author = get_post_field('post_author', $this->security_form_id);
      if($security_form_author != $this->user_id){
        $this->error_message = esc_html__('This security report belongs to another user.', 'pprsus');
        return false;
      }

      $defendant_author = get_post_field('post_author', $this->defendant_id);
      if($defendant_author != $this->user_id){
        $this->error_message = esc_html__('This defendant belongs to another user.', 'pprsus');
        return false;
      }

      //is the token valid
      $token_from_post = get_post_meta((int)$this->security_form_id, 'secret_token', true);
      if($token_from_post != $this->token){
        $this->error_message = esc_html__('There was a problem retrieving the prison list. Please return to the dashboard and try again.', 'pprsus');
        return false;
      }

      return true;
    }

    public function get_query_param($param){
      if(isset($_GET[$param]) && $_GET[$param] != ''){
        return $_GET[$param];
      }

      return false;
    }
  }//end class
}