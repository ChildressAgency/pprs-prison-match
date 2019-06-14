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

      wp_localize_script('pprsus-script', 'pprsus_settings', array(
        'pprsus_ajaxurl' => admin_url('admin-ajax.php'),
      ));
    }

    /**
     * enqueue styles
     */
    public function enqueue_styles(){
      wp_register_style(
        'pprsus-style',
        PPRSUS_PLUGIN_URL . 'css/pprsus-style.css'
      );

      wp_register_style(
        'tablefilter-style',
        PPRSUS_PLUGIN_URL . 'vendors/tablefilter/style/tablefilter.css'
      );

      wp_enqueue_style('tablefilter-style');
      wp_enqueue_style('pprsus-style');
    }

    public function pprsus_change_user(){
      if(!isset($_POST['defendant_id']) || !isset($_POST['user_id']) || !isset($_POST['author_id']) || !isset($_POST['nonce'])){
        wp_send_json_error(esc_html__('There was a problem processing your request. Please refresh the page and try again.', 'pprsus'));
      }

      $defendant_id = $_POST['defendant_id'];
      $user_id = $_POST['user_id'];
      $author_id = $_POST['author_id'];
      $nonce = $_POST['nonce'];

      if(!wp_verify_nonce($nonce, 'update_user' . $defendant_id)){
        wp_send_json_error(esc_html__('There was a problem processing your request. Please refresh the page and try again.', 'pprsus'));
      }

      if(!isset($_POST['new_author']) && !isset($_POST['cancel'])){
        $author_select = '<select class="user-options">';

        $author_select .= $this->get_author_options($author_id);
        $author_select .= '</select>';
        $author_select .= '<span class="profile-icons">';
        $author_select .= '<a href="#" class="user-save" title="' . esc_html__('Save', 'pprsus') . '" data-nonce="' . $nonce . '" data-author_id="' . $author_id . '" data-user_id="' . $user_id . '" data-defendant_id="' . $defendant_id . '"><span class="dashicons dashicons-yes btn-worksheet validated-worksheet"></span></a>';
        $author_select .= '<a href="#" class="user-cancel" title="' . esc_html__('Cancel', 'pprsus') . '" data-nonce="' . $nonce . '" data-author_id="' . $author_id . '" data-user_id="' . $user_id . '" data-defendant_id="' . $defendant_id . '"><span class="dashicons dashicons-no btn-worksheet draft-worksheet"></span></a></span>';

        wp_send_json_success($author_select);
      }
      elseif(isset($_POST['new_author']) && $_POST['new_author'] != '' && !isset($_POST['cancel'])){
        $new_author = $_POST['new_author'];

        $this->update_defendant_author($defendant_id, $author_id, $new_author, 'security');
        $this->update_defendant_author($defendant_id, $author_id, $new_author, 'medical_history');

        wp_update_post(array(
          'ID' => $defendant_id,
          'post_author' => $new_author
        ));

        $new_author_info = get_userdata($new_author);
        $new_author_name = $new_author_info->user_nicename;

        $finished_message = $new_author_name;
        $finished_message .= '<span class="profile-icons">';
        $finished_message .= '<a href="#" class="update-author" title="' . esc_html__('Change User', 'pprsus') . '" data-nonce="' . $nonce . '" data-author_id="' . $new_author . '" data-user_id="' . $user_id . '" data-defendant_id="' . $defendant_id . '">';
        $finished_message .= '<span class="dashicons dashicons-businessman btn-worksheet validated-worksheet"></span>';
        $finished_message .= '</a></span>';

        wp_send_json_success($finished_message);
      }
      else{
        $author_info = get_userdata($author_id);
        $author_name = $author_info->user_nicename;
        $cancel_message = $author_name;
        $cancel_message .= '<span class="profile-icons">';
        $cancel_message .= '<a href="#" class="update-author" title="' . esc_html__('Change User', 'pprsus') . '" data-nonce="' . $nonce . '" data-author_id="' . $author_id . '" data-user_id="' . $user_id . '" data-defendant_id="' . $defendant_id . '">';
        $cancel_message .= '<span class="dashicons dashicons-businessman btn-worksheet validated-worksheet"></span>';
        $cancel_message .= '</a></span>';

        wp_send_json_success($cancel_message);
      }
    }

    private function get_author_options($author_id){
      $group_id = rcpga_group_accounts()->members->get_group_id($this->user_id);
      $group_members = rcpga_group_accounts()->members->get_members($group_id, array(100, 0));
      $options = '';

      foreach($group_members as $member){
        $user_info = get_userdata($member->user_id);
        $user_name = $user_info->user_nicename;
        $selected = '';

        if($member->user_id == $author_id){
          $selected = ' selected';
        }
        $options .= '<option value="' . $member->user_id . '"' . $selected . '>' . $user_name . '</option>';
      }

      return $options;
    }

    private function update_defendant_author($defendant_id, $author_id, $new_author, $post_type){
      $defendant_info_args = array(
        'post_type' => $post_type,
        //'author' => $author_id,
        'posts_per_page' => -1,
        'meta_key' => 'defendant_id',
        'meta_value' => $defendant_id,
        'post_status' => array('publish', 'draft')
      );
      $defendant_info = new WP_Query($defendant_info_args);
      $post_ids = array();

      if($defendant_info->have_posts()){
        while($defendant_info->have_post()){
          $defendant_info->the_post();
          //$post_ids[] = get_the_ID();
          wp_update_post(array(
            'ID' => get_the_ID(),
            'post_author' => $new_author
          ));
        }
      }
      wp_reset_postdata();

      /*foreach($post_ids as $post_id){
        wp_update_post(array(
          'ID' => $post_id,
          'post_author' => $new_author
        ));
      }*/
    }
  }//end class
}