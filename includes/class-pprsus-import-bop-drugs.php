<?php
/**
 * import bop drug list from csv to cpt
 */
if(!defined('ABSPATH')){ exit; }

if(!class_exists('PPRSUS_Import_BOP_Drugs')){
  class PPRSUS_Import_BOP_Drugs{
    /**
     * the acf keys for the drug meta fields
     */
    private $advisories_key = 'field_5d1cb279f73bd';
    private $dose_frequency_key = 'field_5d1cb28df73be';

    public function __construct(){
      $this->init();
    }

    public function init(){
      add_shortcode('pprsus_import_bop_drug_list', array($this, 'process_shortcode'));
    }

    public function process_shortcode(){
      ob_start();

      if(isset($_POST['submit'])){
        if(is_uploaded_file($_FILES['bop-file']['tmp_name'])){
          $file_name = explode('.', $_FILES['bop-file']['name']);
          if(strtolower(end($file_name)) == 'csv'){
            $this->process_csv();
          }
          else{
            $this->show_upload_form(esc_html__('File must be in csv format!', 'pprsus'));
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
      $drug_types = get_terms(array(
        'taxonomy' => 'drug_types',
        'hide_empty' => false
      ));
      ?>
      <form action="<?php echo esc_url(get_permalink()); ?>" method="post" enctype="multipart/form-data">
        <h3><?php echo esc_html__('Import BOP Drug List from csv', 'pprsus'); ?></h3>

        <?php if($upload_error){ echo '<h4>' . $upload_error . '</h4>'; } ?>

        <?php $nonce = wp_create_nonce('import_bop_drug_list'); ?>
        <input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />

        <div class="form-group">
          <label for="bop-file" class="control-label"><?php echo esc_html__('Select csv to import', 'pprsus'); ?></label>
          <input type="file" name="bop-file" id="bop-file" class="form-control" />
        </div>

        <div class="form-group">
          <label for="bop-drug-type" class="control-label"><?php echo esc_html__('Select the drug type.', 'pprsus'); ?></label>
          <select name="bop-drug-type" id="bop-drug-type" class="form-control">
            <option value="">Choose one...</option>
            <?php foreach($drug_types as $drug_type): ?>
              <option value="<?php echo $drug_type->slug; ?>"><?php echo $drug_type->name; ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <button type="submit" name="submit" class="btn btn-primary"><?php echo esc_html__('IMPORT', 'pprsus'); ?></button>
        </div>
      </form>
      <?php
    }

    private function process_csv(){
      //check nonce
      $nonce = $_POST['nonce'];
      if(!wp_verify_nonce($nonce, 'import_bop_drug_list')){
        echo esc_html__('There was a problem processing your request. Please refresh the page and try again.', 'pprsus');
        return false;
      }

      $file_tmp_name = $_FILES['bop-file']['tmp_name'];
      $file_name = $_FILES['bop-file']['name'];

      $upload_dir = wp_upload_dir();
      $upload_dir_base = $upload_dir['basedir'];
      $file_folder = $upload_dir_base . '/bop_drug_lists/';
      $file_path = $file_folder . $file_name;

      move_uploaded_file($file_tmp_name, $file_path);

      return $this->import_csv($file_path);
    }

    private function import_csv($file_path){
      $bop_file = fopen($file_path, 'r');
      $head = fgetcsv($bop_file);
      $drug_type = $_POST['bop-drug-type'];

      while(($column = fgetcsv($bop_file)) !== false){
        $column = array_combine($head, $column);

        $drug_name = $column['drug_name'];
        $advisories = $column['advisories'];
        $dose_frequency = $column['dose_frequency'];

        $drug_id = $this->add_drug($drug_name, $drug_type);

        if(is_wp_error($drug_id)){
          echo esc_html__('Could not import ', 'pprsus') . $drug_name . '<br />' . $drug_id->get_error_message();
          continue;
        }
        
        $this->add_drug_meta($drug_id, $advisories, $dose_frequency);
        echo $drug_name . esc_html__(' was imported.', 'pprsus') . '<br />';
      }

      fclose($bop_file);
      unlink($file_path);
      echo esc_html__('Import complete.', 'pprsus');
    }

    private function add_drug($drug_name, $drug_type){
      //$drug_type = $_POST['bop-drug-type'];
      $new_drug_args = array(
        'post_type' => 'bop_drugs',
        'post_title' => wp_strip_all_tags($drug_name),
        'post_status' => 'publish',
        //'tax_input' => array(
        //  'drug_types' => array($drug_type)
        //)
      );
      
      //return wp_insert_post($new_drug_args);
      $new_drug_id = wp_insert_post($new_drug_args);
      wp_set_object_terms($new_drug_id, $drug_type, 'drug_types');
      return $new_drug_id;
    }

    private function add_drug_meta($drug_id, $advisories, $dose_frequency){
      update_field($this->advisories_key, $advisories, $drug_id);
      update_field($this->dose_frequency_key, $dose_frequency, $drug_id);
    }
  }//end class
}