<?php acf_form_head(); ?>

<?php get_header(); ?>
<section class="blog-section">
  <div class="container">
    <article id="post-<?php echo get_the_ID(); ?>" class="post" data-wow-delay=".4s">
      <div class="post-content">
        <div class="entry-content">
          <?php 
            if(!is_user_logged_in()){
              echo apply_filters('the_content', wp_kses_post(get_option('options_login_message')));
              echo sprintf(wp_kses_post(__('Please <a href="%1$s">Login</a>', 'pprsus'), array('a' => array('href' => array()))), wp_login_url(home_url('dashboard')));
            }
            else{
              if(have_posts()){
                while(have_posts()){
                  the_post();
                  the_content();
                }
              }
              
              do_action('pprsus_to_dashboard_btn');
              do_action('pprsus_before_worksheet');

              do_action('pprsus_worksheet');
            }
          ?>
        </div>
      </div>
    </article>
  </div>
</section>
<?php get_footer();