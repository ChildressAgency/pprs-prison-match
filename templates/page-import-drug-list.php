<?php get_header(); ?>

<?php
  if(!current_user_can('administrator')){
    wp_redirect(home_url());
  }
?>

<section class="blog-section">
  <div class="container">
    <article id="post-<?php echo get_the_ID(); ?>" class="post" data-wow-delay=".4s">
      <div class="post-content">
        <div class="entry-content">
          
          

        </div>
      </div>
    </article>
  </div>
</section>
<?php get_footer();