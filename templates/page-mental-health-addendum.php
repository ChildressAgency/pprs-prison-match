<?php get_header(); ?>
<section class="blog-section">
  <div class="container">
    <article id="post-<?php echo get_the_ID(); ?>" class="post" data-wow-delay=".4s">
      <div class="post-content">
        <div class="entry-content">
          <?php
            if(have_posts()){
              while(have_posts()){
                the_post();

                the_content();
              }
            }

          if(have_rows('mental_health_questions')): ?>
            <div class="panel-group" id="mental-health-addendum" role="tablist" aria-multiselectable="true">
              <?php $i = 1; while(have_rows('mental_health_questions')): the_row(); ?>
                <div class="panel panel-default">
                  <div id="heading-<?php echo $i; ?>" class="panel-heading" role="tab">
                    <h4 class="panel-title">
                      <a href="#panel-<?php echo $i; ?>" class="collapsed" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="panel-<?php echo $i; ?>"><?php echo apply_filters('the_content', wp_kses_post(get_sub_field('question'))); ?></a>
                    </h4>
                  </div>
                  <div id="panel-<?php echo $i; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-<?php echo $i; ?>">
                    <div class="panel-body">
                      <?php echo apply_filters('the_content', wp_kses_post(get_sub_field('question_content'))); ?>
                    </div>
                  </div>
                </div>
              <?php $i++; endwhile; ?>
            </div>
          <?php endif; ?>            
        </div>
      </div>
    </article>
  </div>
</section>
<?php get_footer();