<?php
/**
 * Import prison data from csv to cpt
 */
if(!defined('ABSPATH')){ exit; }

if(!class_exists('PPRSUS_Import_Prison_Data')){
  class PPRSUS_Import_Prison_Data{
    /**
     * the acf keys for the prison data meta fields
     */
    private $state_key = 'field_5d234a53b2aa5';
    private $facility_website_key = 'field_5d234a9fb2aa7';
    private $inmate_population_gender_key = 'field_5d234aaab2aa8';
    private $attributes_key = 'field_5d234afeb2aaa';
    private $national_programs_key = 'field_5d234b3bb2aab';
    private $occupational_training_key = 'field_5d234b71b155c';

    public function __construct(){
      $this->init();
    }

    public function init(){
      add_shortcode('pprsus_import_prison_data', array($this, 'process_shortcode'));
    }

    public function process_shortcode(){
      ob_start();

      if(isset($_POST['submit'])){
        if(is_uploaded_file($_FILES['prison-file']['tmp_name'])){
          $file_name = explode('.', $_FILES['prison-file']['name']);
          if(strtolower(end($file_name)) == 'csv'){
            $this->process_csv();
          }
          else{
            $this->show_upload_form(esc_html__('File must be in csv format', 'pprsus'));
          }
        }
        else{
          $this->show_upload_form(esc_html__('No file selected!', 'pprsus'));
        }
      }
      else{
        $this->show_upload_form();
      }

      return ob_get_clean();
    }

    private function show_upload_form($upload_error = null){
      ?>

      <form action="<?php echo esc_url(get_permalink()); ?>" method="post" enctype="multipart/form-data">
        <h3><?php echo esc_html__('Import Prison Data from csv', 'pprsus'); ?></h3>

        <?php if($upload_error){ echo '<h4>' . $upload_error . '</h4>'; } ?>

        <?php $nonce = wp_create_nonce('import_prison_data_list'); ?>
        <input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />

        <div class="form-group">
          <label for="prison-file" class="control-label"><?php echo esc_html__('Select csv to Import', 'pprsus'); ?></label>
          <input type="file" name="prison-file" id="prison-file" class="form-control" />
        </div>

        <div class="from-group">
          <button type="submit" name="submit" class="btn btn-primary"><?php echo esc_html__('IMPORT', 'pprsus'); ?></button>
        </div>
      </form>

      <?php
    }

    private function process_csv(){
      //check nonce
      $nonce = $_POST['nonce'];
      if(!wp_verify_nonce($nonce, 'import_prison_data_list')){
        echo esc_html__('There was a problem processing your request. Please refresh the page and try again.', 'pprsus');
        return false;
      }

      $file_tmp_name = $_FILES['prison-file']['tmp_name'];
      $file_name = $_FILES['prison-file']['name'];

      $upload_dir = wp_upload_dir();
      $upload_dir_base = $upload_dir['basedir'];
      $file_folder = $upload_dir_base . '/prison_data/';
      $file_path = $file_folder . $file_name;

      move_uploaded_file($file_tmp_name, $file_path);

      return $this->import_csv($file_path);
    }

    private function import_csv($file_path){
      $prison_file = fopen($file_path, 'r');
      $head = fgetcsv($prison_file);

      while(($column = fgetcsv($prison_file)) !== false){
        $column = array_combine($head, $column);

        $prison_name = esc_html($column['Facility Name']);

        $prison_id = $this->add_prison($prison_name);
        if(is_wp_error($prison_id)){
          echo esc_html__('Could not import ', 'pprsus') . $prison_name . '<br />' . $prison_id->get_error_message() . '<br />';
          continue;
        }

        $security_level_id = $this->get_level_id($column['Security Level'], 'security_level');
        $facility_care_level_id = $this->get_level_id($column['Facility Care Level'], 'facility_care_level');

        wp_set_object_terms($prison_id, $security_level_id, 'security_level');
        wp_set_object_terms($prison_id, $facility_care_level_id, 'facility_care_level');

        $this->add_prison_meta($prison_id, $column);
        echo $prison_name . esc_html__(' was imported.', 'pprsus') . '<br />';
      }

      fclose($prison_file);
      unlink($file_path);
      echo esc_html__('Import Complete', 'pprsus');
    }

    private function add_prison_meta($prison_id, $column){
      update_field($this->state_key, $column['Dropdown'], $prison_id);
      update_field($this->facility_website_key, $column['Facility Website'], $prison_id);
      update_field($this->inmate_population_gender_key, $column['Inmate Population Gender'], $prison_id);
      update_field($this->attributes_key, explode(', ', $column['Attributes']), $prison_id);
      update_field($this->national_programs_key, explode(', ', $column['National Programs']), $prison_id);
      update_field($this->occupational_training_key, explode(', ', $column['Occupational Training']), $prison_id);
    }

    private function get_level_id($term_name = null, $tax){
      if($term_name == null){
        return null;
      }

      $term = get_term_by('name', $term_name, $tax);

      return (int)$term->term_id;
    }

    private function add_prison($prison_name){
      $new_prison_args = array(
        'post_type' => 'prison_data',
        'post_title' => wp_strip_all_tags($prison_name),
        'post_status' => 'publish'
      );

      $new_prison_id = wp_insert_post($new_prison_args);
      return $new_prison_id;
    }
  }//end class
}