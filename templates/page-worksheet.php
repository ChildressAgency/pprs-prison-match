<?php acf_form_head(); ?>

<?php get_header(); ?>
<section class="blog-section">
  <div class="container">
    <article id="post-<?php echo get_the_ID(); ?>" class="post" data-wow-delay=".4s">
      <div class="post-content">
        <div class="entry-content">
          <?php 
            do_action('pprsus_to_dashboard_btn');
            do_action('pprsus_before_worksheet');

            do_action('pprsus_worksheet');
          ?>
        </div>
      </div>
    </article>
  </div>
</section>
<?php get_footer();