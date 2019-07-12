<?php get_header(); ?>
<section class="blog-section">
  <div class="container">
    <article id="post-<?php echo get_the_ID(); ?>" class="post" data-wow-delay=".4s">
      <div class="post-content">
        <div class="entry-content">
          <?php
            if(!is_user_logged_in()){
              echo apply_filters('the_content', wp_kses_post(get_option('options_login_message')));

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
                  __('Please <a href="%1$s">Sign up</a> for one of our membership plans to continue.', 'pprsus'),
                  array('a' => array('href' => array()))
                ),
                esc_url(home_url('register'))
              );
            }
            else{
              if(have_posts()){
                while(have_posts()){
                  the_post();
                  echo '<h2>' . esc_html(get_the_title()) . '</h2>';
                  $region = get_field_object('region');
                  if($region){
                    $region_value = $region['value'];
                    $region_label = $region['choices'][$region_value];

                    echo '<p><strong>Region:</strong> ' . esc_html($region_label) . '</p>';
                  }
                  echo '<p><strong>State:</strong> ' . esc_html(get_field('state')) . '</p>';
                  echo '<p><strong>Facility Website:</strong> ' . esc_html(get_field('facility_web_site')) . '</p>';
                  echo '<p><strong>Inmate Population Gender:</strong> ' . esc_html(get_field('inmate_population_gender')) . '</p>';

                  $attributes = get_field('attributes');
                  if($attributes){
                    echo '<p><strong>Attributes:</strong> ' . implode(', ', $attributes) . '</p>';
                  }

                  $national_programs = get_field('national_programs');
                  if($national_programs){
                    echo '<p><strong>National Programs:</strong> ' . implode(', ', $national_programs) . '</p>';
                  }

                  $occupational_training = get_field('occupational_training');
                  if($occupational_training){
                    echo '<p><strong>Occupational Training:</strong> ' . implode(', ', $occupational_training) . '</p>'; 
                  }

                  $pdf = get_field('prison_report');
                  if($pdf){
                    echo '<p><a href="' . $pdf['url'] . '">' . esc_html__('View Prison Report', 'pprsus') . '</a></p>';
                  }
                }
              }
            }
          ?>
        </div>
      </div>
    </article>
  </div>
</section>
<?php get_footer();