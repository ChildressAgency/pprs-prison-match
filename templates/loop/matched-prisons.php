<?php
/**
 * template for displaying the matched prisons
 */
if(!defined('ABSPATH')){ exit; }
?>

<?php foreach($prison_list as $prison): ?>
<div class="prison">
  <h3><?php echo esc_html(get_the_title($prison->ID)) . ' id=' . $prison->ID; ?></h3>
  
</div>
<?php endforeach;