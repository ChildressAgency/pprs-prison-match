<?php get_header(); ?>
<section class="blog-section">
  <div class="container">
    <article id="post-<?php echo get_the_ID(); ?>" class="post" data-wow-delay=".4s">
      <div class="post-content">
        <div class="entry-content">
          <?php
            if(!is_user_logged_in()){
              echo apply_filters('the_content', wp_kses_post(get_option('options_login_message')));
              //echo sprintf(wp_kses_post(__('Please <a href="%1$s">Login</a> or <a href="%2$s">Register</a> to begin.', 'pprsus'), array('a' => array('href' => array()))), wp_login_url(home_url('dashboard')));

              echo sprintf(
                wp_kses_post(
                  __('Please <a href="%1$s">Login</a> or <a href="%2$s">Register</a> to begin.', 'pprsus'), 
                  array('a' => array('href' => array()))
                ), 
                wp_login_url(home_url('dashboard')),
                esc_url(home_url('register'))
              );
            }
            elseif(!rcp_user_has_paid_membership()){
              echo sprintf(
                wp_kses_post(
                  __('Please <a href="%1$s">sign up</a> for one of our membership plans to continue.', 'pprsus'),
                  array('a' => array('href' => array()))
                ),
                esc_url(home_url('register'))
              );
            }
            else{
              echo '<h3>' . esc_html__('PPRSUS Prison Match', 'pprsus') . '</h3>';
              $announcement = get_post_meta(get_the_ID(), 'special_announcement', true);
              if($announcement){
                echo '<div class="announcement">';
                echo apply_filters('the_content', wp_kses_post($announcement));
                echo '</div>';
              }
              echo '<p>' . esc_html__('Create or edit your client profiles.', 'pprsus') . '</p>';
              if(rcpga_group_accounts()->members->is_group_owner() || rcpga_group_accounts()->members->is_group_admin()){
                echo '<p>' . 
                  sprintf(
                    wp_kses_post(
                      __('<a href="%1$s">Click here</a> to manage users for your Firm\'s account', 'pprsus'),
                      array('a' => array('href' => array()))
                    ),
                    esc_url(home_url('firm-management'))
                  ) .
                  '</p>';
              }
            
              do_action('pprsus_before_dashboard');

              do_action('pprsus_dashboard');

              do_action('pprsus_after_dashboard');
            }
          ?>
        </div>
      </div>
    </article>
  </div>
</section>
<?php get_footer();