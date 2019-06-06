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
            echo '<td>' . get_the_author_meta('display_name') . '</td>';
            $this->output_defendant_field($defendant_id);
            $this->output_medical_history_field($defendant_id);
            $this->output_security_field($defendant_id);

          echo '</tr>';
        }
      }

      echo '</tbody>';
      echo '    </table>
              </div>
            </div>';
    }

    private function output_defendant_field($defendant_id){
      $defendant_name = get_the_title($defendant_id);
      $link_args = array(
        'post_type' => $defendant_id,
        'defendant_id' => $defendant_id,
        'step' => 1,
        'token' => get_post_meta($defendant_id, 'secret_token', true),
        'form_type' => 'defendants'
      );

      $link_class = array();
      $link_class[] = 'dashicons';
      $link_class[] = 'dashicons-welcome-write-blog';
      $link_class[] = 'btn-worksheet';
      $link_class[] = (get_post_status() == 'publish') ? 'validated-worksheet' : 'draft-worksheet';

      $edit_defendant_link_class = implode(' ', $link_class);

      $edit_defendant_link = add_query_arg($link_args, home_url('worksheet'));
      echo '<td>' . $defendant_name . '<a href="' . $edit_defendant_link . '" class="" title="Edit Defendant Profile"><span class="' . $edit_defendant_link_class . '"></span></a></td>';
    }

    private function output_medical_history_field($defendant_id){

    }

    private function output_security_field($defendant_id){
      
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