<?php
/**
 * The Dashboard
 */
if(!defined('ABSPATH')){ exit; }

if(!class_exists('PPRSUS_Dashboard')){
  class PPRSUS_Dashboard{

    private $user_id;

    public function __construct(){
      $this->user_id = get_current_user_id();

      add_action('init', array($this, 'output_dashboard'));
    }

    public function output_dashboard(){
      echo '<div class="dashboard">
              <div class="table-responsive">
                <table id="psr-table" class="table table-striped pst-table">';
      echo '<thead>
              <tr>
                <th>' . esc_html__('User Name', 'pprsus') . '</th>
                <th>' . esc_html__('Defendant', 'pprsus') . '</th>
                <th>' . esc_html__('Medical History', 'pprsus') . '</th>
                <th>' . esc_html__('Security', 'pprsus') . '</th>
                <th>' . esc_html__('Date Created', 'pprsus') . '</th>
              </tr>
            </thead>';
      echo '<tbody>';

      $defendants = $this->get_defendants();
      if($defendants->have_posts()){
        while($defendants->have_posts()){
          $defendants->the_post();
          $defendant_id = get_the_ID();
          echo '<tr>';
            echo '<td>' . esc_html(get_the_author_meta('display_name')) . '</td>';
            $this->output_defendant_field($defendant_id);
            $this->output_medical_history_field($defendant_id);
            $this->output_security_field($defendant_id);
            echo '<td>' . esc_html(get_post_meta(get_the_ID(), 'date_created', true)) . '</td>';
          echo '</tr>';
        }
      } 
      wp_reset_postdata();

      echo '</tbody>';
      echo '    </table>
              </div>
            </div>';
    }

    private function output_defendant_field($defendant_id){
      $defendant_name = get_the_title($defendant_id);
      $edit_link_args = array(
        'post_id' => $defendant_id,
        'defendant_id' => $defendant_id,
        'step' => 1,
        'token' => get_post_meta($defendant_id, 'secret_token', true),
        'form_type' => 'defendants'
      );
      $edit_defendant_link = add_query_arg($edit_link_args, home_url('worksheet'));

      $edit_link_class = array();
      $edit_link_class[] = 'dashicons';
      $edit_link_class[] = 'dashicons-welcome-write-blog';
      $edit_link_class[] = 'btn-worksheet';
      $edit_link_class[] = (get_post_status() == 'publish') ? 'validated-worksheet' : 'draft-worksheet';

      $edit_defendant_link_class = implode(' ', $edit_link_class);

      $view_link_args = array(
        'defendant_id' => $defendant_id
      );
      $view_defendant_link = add_query_arg($view_link_args, home_url('review-information'));

      $view_link_class = array();
      $view_link_class[] = 'dashicons';
      $view_link_class[] = 'dashicons-visibility';
      $view_link_class[] = 'btn-worksheet';
      $view_link_class[] = (get_post_status() == 'publish') ? 'validated-worksheet' : 'draft-worksheet';

      $view_defendant_link_class = implode(' ', $view_link_class);

      echo '<td>';
        echo esc_html($defendant_name);
        echo '<a href="' . $edit_defendant_link . '" class="" title="' . esc_html__('Edit Defendant Profile', 'pprsus') . '"><span class="' . $edit_defendant_link_class . '"></span></a>';
        echo '<a href="' . $view_defendant_link . '" class="" title="' . esc_html__('View Defendant Report', 'pprsus') . '"><span class="' . $view_defendant_link_class . '"></span></a>';
      echo '</td>';
    }

    private function output_medical_history_field($defendant_id){
      $medical_query_args = array(
        'post_type' => 'medical_history',
        'author' => $this->user_id,
        'posts_per_page' => 1,
        'meta_key' => 'defendant_id',
        'meta_value' => $defendant_id
      );

      $medical_history = new WP_Query($medical_query_args);
      if($medical_history->have_posts()){
        while($medical_history->have_posts()){
          $medical_history->the_post();
          $edit_link_args = array(
            'post_id' => get_the_ID(),
            'defendant_id' => $defendant_id,
            'step' => 1,
            'token' => get_post_meta(get_the_ID(), 'secret_token', true),
            'form_type' => 'medical_history'
          );
          $edit_medical_link = add_query_arg($edit_link_args, home_url('worksheet'));

          $edit_link_class = array();
          $edit_link_class[] = 'dashicons';
          $edit_link_class[] = 'dashicons-welcome-write-blog';
          $edit_link_class[] = 'btn-worksheet';
          $edit_link_class[] = (get_post_status() == 'publish') ? 'validated-worksheet' : 'draft-worksheet';

          $edit_medical_link_class = implode(' ', $edit_link_class);

          echo '<td><a href="' . $edit_medical_link . '" class="" title="' . esc_html__('Edit Medical History', 'pprsus') . '"><span class="' . $edit_medical_link_class . '"></span></td>';
        }
      }
      else{
        $link_args = array(
          'form_type' => 'medical_history',
          'step' => 1,
          'defendant_id' => $defendant_id
        );
        $add_medical_link = add_query_arg($link_args, home_url('worksheet'));

        $link_class = array();
        $link_class[] = 'dashicons';
        $link_class[] = 'dashicons-welcome-add-page';
        $link_class[] = 'btn-worksheet';
        $link_class[] = 'draft-worksheet';

        $add_medical_link_class = implode(' ', $link_class);

        echo '<td><a href="' . $add_medical_link . '" class="" title="' . esc_html__('Add Medical History', 'pprsus') . '"><span class="' . $add_medical_link_class . '"></span></a></td>';
      }
      wp_reset_postdata();
    }

    private function output_security_field($defendant_id){
      $security_query_args = array(
        'post_type' => 'security',
        'author' => $this->user_id,
        'posts_per_page' => 1,
        'meta_key' => 'defendant_id',
        'meta_value' => $defendant_id
      );

      $security = new WP_Query($security_query_args);
      if($security->have_posts()){
        while($security->have_posts()){
          $security->the_post();

          $edit_link_args = array(
            'post_id' => get_the_ID(),
            'defendant_id' => $defendant_id,
            'step' => 1,
            'token' => get_post_meta(get_the_ID(), 'secret_token', true),
            'form_type' => 'security'
          );
          $edit_security_link = add_query_arg($edit_link_args, home_url('worksheet'));

          $edit_link_class = array();
          $edit_link_class[] = 'dashicons';
          $edit_link_class[] = 'dashicons-welcome-write-blog';
          $edit_link_class[] = 'btn-worksheet';
          $edit_link_class[] = (get_post_status() == 'publish') ? 'validated-worksheet' : 'draft-worksheet';

          $edit_security_link_class = implode(' ', $edit_link_class);

          echo '<td><a href="' . $edit_security_link . '" class="" title="' . esc_html__('Edit Security Information', 'pprsus') . '"><span class="' . $edit_security_link_class . '</span></a></td>';
        }
      }
      else{
        $link_args = array(
          'form_type' => 'security',
          'step' => 1,
          'defendant_id' => $defendant_id
        );
        $add_security_link = add_query_arg($link_args, home_url('worksheet'));

        $link_class= array();
        $link_class[] = 'dashicons';
        $link_class[] = 'dashicons-welcome-add-page';
        $link_class[] = 'btn-worksheet';
        $link_class[] = 'draft-worksheet';

        $add_security_link_class = implode(' ', $link_class);

        echo '<td><a href="' . $add_security_link . '" class="" title="' . esc_html__('Add Security Information', 'pprsus') . '"><span class="' . $add_security_link_class . '"></span></a></td>';
      }
      wp_reset_postdata();
    }

    private function get_defendants(){
      $defendants_query_args = array(
        'post_type' => 'defendants',
        'author' => $this->user_id,
        'posts_per_page' => -1,
        'post_status' => 'publish'
      );

      return new WP_Query($defendants_query_args);
    }
  }//end class
}