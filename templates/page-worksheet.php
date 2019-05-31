<?php acf_form_head(); ?>

<?php get_header(); ?>
<section class="blog-section">
  <div class="container">
    <div class="row">
      <div class="col-xs-12">
        <article id="post-<?php echo get_the_ID(); ?>" class="post" data-wow-delay=".4s">
          <div class="post-content">
            <div class="entry-content">
              <?php 
                do_action('pprsus_to_dashboard_btn');
                do_action('pprsus_before_worksheet');

                do_action('pprsus_worksheet');

                /*
                $form_type = '';
                if(isset($_GET['form_type'])){
                  $form_type = $_GET['form_type'];

                  switch($form_type){
                    case 'defendants':
                      do_action('pprsus_defendants_worksheet');
                    break;

                    case 'medical_history':
                      do_action('pprsus_medical_history_worksheet');
                    break;

                    case 'security':
                      do_action('pprsus_security_worksheet');
                    break;

                    default:
                      wp_safe_redirect(esc_url(home_url('dashboard')));
                  }
                }
                else{
                  wp_safe_redirect(esc_url(home_url('dashboard')));
                }*/
              ?>
            </div>
          </div>
        </article>
      </div>
    </div>
  </div>
</section>
<?php get_footer();