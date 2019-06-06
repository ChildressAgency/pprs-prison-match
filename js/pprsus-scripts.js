jQuery(document).ready(function($){
  if(document.getElementById('psr-table')){
    var dashboard_filter = new TableFilter(document.querySelector('.psr-table'), {
      base_path: tablefilter_settings.tablefilter_basepath,
      filters_row_index: 1,
      clear_filter_text: tablefilter_settings.tablefilter_clear_text,
      col_0: 'select',
      col_1: 'select',
      col_2: 'none',
      col_3: 'none',
      col_4: 'select'
    });
    dashboard_filter.init();
  }

  $('.pprsus-submit').on('click', function(){
    var direction = $(this).attr('name');
    $('#direction').val(direction);
    $('.acf-form').submit();
  });
});