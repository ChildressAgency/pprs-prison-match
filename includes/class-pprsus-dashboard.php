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

      //add_action('init', array($this, 'output_dashboard'));
      $this->output_dashboard();
    }

    public function output_dashboard(){
      if(isset($_GET['delete']) && $_GET['delete'] == 1){
        $this->delete_defendant();
      }
      echo '<div class="dashboard">
              <div class="table-responsive">
                <table id="psr-table" class="table table-striped psr-table">';
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
          $author_name = get_the_author_meta('display_name');
          $author_id = get_the_author_meta('ID');
          echo '<tr>';
            //echo '<td>' . esc_html(get_the_author_meta('display_name')) . '</td>';
            $this->output_user_name_field($defendant_id, $author_name, $author_id);
            $this->output_defendant_field($defendant_id);
            $this->output_medical_history_field($defendant_id);
            $this->output_security_field($defendant_id);
            echo '<td>' . wp_kses_post(get_post_meta($defendant_id, 'date_created', true)) . '</td>';
          echo '</tr>';
        }
      } 
      wp_reset_postdata();

      echo '</tbody>';
      echo '    </table>
              </div>
            </div>';
    }

    private function output_user_name_field($defendant_id, $author_name, $author_id){
      echo '<td><span id="user_name">' . $author_name;

      if(rcpga_group_accounts()->members->is_group_owner($this->user_id) || rcpga_group_accounts()->members->is_group_admin($this->user_id)){
        $nonce = wp_create_nonce('update_user' . $defendant_id);

        echo '<span class="profile-icons"><a href="#" class="update-author" title="' . esc_html__('Change User', 'pprsus') . '" data-nonce="' . $nonce . '" data-author_id="' . $author_id . '" data-user_id="' . $this->user_id . '" data-defendant_id="' . $defendant_id . '">';
        echo '<span class="dashicons dashicons-businessman btn-worksheet validated-worksheet"></span>';
        echo '</a></span>';
      }

      echo '</span></td>';
    }

    private function output_defendant_field($defendant_id){
      $defendant_name = get_the_title($defendant_id);
      //edit defendant link settings
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

      //view defendant link settings
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

      //delete defendant link settings
      $delete_link_args = $edit_link_args;
      $delete_nonce = wp_create_nonce('delete_defendant_' . $defendant_id);
      $delete_link_args['delete'] = 1;
      $delete_link_args['nonce'] = $delete_nonce;
      $delete_defendant_link = add_query_arg($delete_link_args, home_url('dashboard'));

      $delete_link_class = array();
      $delete_link_class[] = 'dashicons';
      $delete_link_class[] = 'dashicons-trash';
      $delete_link_class[] = 'btn-worksheet';
      $delete_link_class[] = 'validated-worksheet';

      $delete_defendant_link_class = implode(' ', $delete_link_class);

      echo '<td>';
        echo esc_html($defendant_name);
        echo '<span class="profile-icons"><a href="' . $edit_defendant_link . '" class="" title="' . esc_html__('Edit Defendant Profile', 'pprsus') . '"><span class="' . $edit_defendant_link_class . '"></span></a>';
        echo '<a href="' . $view_defendant_link . '" class="" title="' . esc_html__('View Defendant Report', 'pprsus') . '"><span class="' . $view_defendant_link_class . '"></span></a>';
        echo '<a href="' . $delete_defendant_link . '" class="" title="' . esc_html__('Delete Defendant', 'pprsus') . '"><span class="' . $delete_defendant_link_class . '"></span></a>';
      echo '</span></td>';
    }

    private function output_medical_history_field($defendant_id){
      $medical_query_args = array(
        'post_type' => 'medical_history',
        //'author' => $this->user_id,
        'posts_per_page' => 1,
        'meta_key' => 'defendant_id',
        'meta_value' => $defendant_id,
        'post_status' => array('publish', 'draft')
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

          echo '<td style="text-align:center;"><a href="' . $edit_medical_link . '" class="" title="' . esc_html__('Edit Medical History', 'pprsus') . '"><span class="' . $edit_medical_link_class . '"></span></td>';
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

        echo '<td style="text-align:center;"><a href="' . $add_medical_link . '" class="" title="' . esc_html__('Add Medical History', 'pprsus') . '"><span class="' . $add_medical_link_class . '"></span></a></td>';
      }
      wp_reset_postdata();
    }

    private function output_security_field($defendant_id){
      $security_query_args = array(
        'post_type' => 'security',
        //'author' => $this->user_id,
        'posts_per_page' => 1,
        'meta_key' => 'defendant_id',
        'meta_value' => $defendant_id,
        'post_status' => array('publish', 'draft')
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

          echo '<td style="text-align:center;"><a href="' . $edit_security_link . '" class="" title="' . esc_html__('Edit Security Information', 'pprsus') . '"><span class="' . $edit_security_link_class . '</span></a></td>';
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

        echo '<td style="text-align:center;"><a href="' . $add_security_link . '" class="" title="' . esc_html__('Add Security Information', 'pprsus') . '"><span class="' . $add_security_link_class . '"></span></a></td>';
      }
      wp_reset_postdata();
    }

    private function get_defendants(){
      $authors = array($this->user_id);

      if(rcpga_group_accounts()->members->is_group_owner($this->user_id) || rcpga_group_accounts()->members->is_group_admin($this->user_id)){
        $group_id = rcpga_group_accounts()->members->get_group_id($this->user_id);
        $group_members = rcpga_group_accounts()->members->get_members($group_id, array(100, 0));
        
        foreach($group_members as $group_member){
          $authors[] = $group_member->user_id;
        }
      }

      $defendants_query_args = array(
        'post_type' => 'defendants',
        'author__in' => $authors,
        'posts_per_page' => -1,
        'post_status' => array('publish', 'draft')
      );

      return new WP_Query($defendants_query_args);
    }

    private function delete_defendant(){
      if($this->user_can_modify_defendant()){
        $defendant_id = $_GET['defendant_id'];
        $defendant_name = get_the_title($defendant_id);
        $nonce = $_GET['nonce'];

        if(!wp_verify_nonce($nonce, 'delete_defendant_' . $defendant_id)){
          echo esc_html__('There was a problem processing your request. Please refresh the page and try again.', 'pprsus');
        }
        else{
          $this->delete_defendant_info($defendant_id, 'security');
          $this->delete_defendant_info($defendant_id, 'medical_history');

          wp_delete_post($defendant_id, true);

          echo $defendant_name . ' deleted.';
        }
      }
    }

    private function delete_defendant_info($defendant_id, $post_type){
      $defendant_info_args = array(
        'post_type' => $post_type,
        //'author' => $this->user_id,
        'posts_per_page' => -1,
        'meta_key' => 'defendant_id',
        'meta_value' => $defendant_id,
        'post_status' => array('publish', 'draft')
      );
      $defendant_info = new WP_Query($defendant_info_args);

      if($defendant_info->have_posts()){
        while($defendant_info->have_posts()){
          $defendant_info->the_post();
          wp_delete_post(get_the_ID(), true);
        }
      }
    }

    private function user_can_modify_defendant(){
      if(!isset($_GET['post_id']) || !isset($_GET['defendant_id']) || !isset($_GET['token'])){
        return false;
      }

      $worksheet_author = get_post_field('post_author', $_GET['defendant_id']);
      if($this->user_id != $worksheet_author){
        return false;
      }

      $token_from_url = sanitize_text_field($_GET['token']);
      $token_from_post_meta = get_post_meta((int)$_GET['defendant_id'], 'secret_token', true);
      if($token_from_url != $token_from_post_meta){
        return false;
      }

      return true;
    }
  }//end class
}