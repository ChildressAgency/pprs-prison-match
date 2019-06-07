<?php get_header(); ?>
<section class="blog-section">
  <div class="container">
    <article id="post-<?php echo get_the_ID(); ?>" class="post" data-wow-delay=".4s">
      <div class="post-content">
        <div class="entry-content">
          <?php
            if(!is_user_logged_in()){
              echo apply_filters('the_content', wp_kses_post(get_option('options_login_message')));
              echo sprintf(esc_html__('Please <a href="%1$s">Login</a>', 'pprsus'), wp_login_url(home_url('dashboard')));
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