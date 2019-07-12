<?php
/**
 * template for displaying the matched prisons
 */
if(!defined('ABSPATH')){ exit; }
?>

<div class="dashboard">
  <div class="table-responsive">
    <table id="prison-list" class="table table-striped">
      <thead>
        <tr>
          <th><?php echo esc_html__('Prison Name', 'pprsus'); ?></th>
          <th><?php echo esc_html__('Distance', 'pprsus'); ?></th>
          <th><?php echo esc_html__('More Information', 'pprsus'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($prison_list as $prison): ?>
          <tr>
            <td><?php echo esc_html(get_the_title($prison->ID)); ?></td>
            <td><?php echo round($prison->distance) . ' miles'; ?></td>
            <td><a href="<?php echo esc_url(get_permalink($prison->ID)); ?>">View More</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
