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
              do_action('pprsus_to_dashboard_btn');
              do_action('pprsus_before_review_information');
              do_action('pprsus_review_information');
              do_action('pprsus_after_review_information');
            }
          ?>
        </div>
      </div>
    </article>
  </div>
</section>
<?php get_footer();