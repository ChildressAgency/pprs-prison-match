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
            <ol type="I">
              <?php $i = 1; while(have_rows('mental_health_questions')): the_row(); ?>
                <li class="bop-question section_<?php echo $i; ?>">

                  <?php if(have_rows('question_parts')): $p = 0; while(have_rows('question_parts')): the_row(); ?>

                    <?php if(get_sub_field('question_part_type') == 'Question'): ?>
                    
                      <?php if($p > 0){ echo '<div id="section_' . $i . '-' . $p . '" class="collapse">'; } ?>
                        <?php echo apply_filters('the_content', wp_kses_post(get_sub_field('question_part'))); ?>
                          <button type="button" class="button-primary" data-toggle="collapse" data-target="#section_<?php echo $i; ?>-<?php echo $p + 1; ?>" aria-expanded="false" aria-controls="section_<?php echo $i; ?>-<?php echo $p + 1; ?>">Yes</button>
                      <?php if($p > 0){ echo '</div>'; } ?>

                    <?php else: ?>

                      <div id="section_<?php echo $i; ?>-<?php echo $p; ?>" class="collapse">
                        <?php echo apply_filters('the_content', wp_kses_post(get_sub_field('program_content'))); ?>
                      </div>

                    <?php endif; ?>

                  <?php $p++; endwhile; endif; ?>

                </li>
              <?php $i++; endwhile; ?>
            </ol>
          <?php endif; ?>
            
        </div>
      </div>
    </article>
  </div>
</section>
<?php get_footer();