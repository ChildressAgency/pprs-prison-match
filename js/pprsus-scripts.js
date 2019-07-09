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

  $('[data-toggle="tooltip"]').tooltip();

  $('.pprsus-submit').on('click', function(){
    var direction = $(this).attr('name');
    $('#direction').val(direction);
    $('#pprsus-worksheet').submit();
  });

  //change user
  $('#user_name').on('click', '.update-author, .user-save, .user-cancel', function(e){
    e.preventDefault();
    var $clickedLink = $(this);
    var profileData = get_profile_data($clickedLink);

    if($clickedLink.hasClass('user-save')){
      profileData['new_author'] = $('.user-options').val();
    }

    if($clickedLink.hasClass('user-cancel')){
      profileData['cancel'] = 1;
    }

    process_request(profileData);
  });

  function get_profile_data($profile){
    var data = {
      'action': 'pprsus_change_user',
      'nonce': $profile.data('nonce'),
      'author_id': $profile.data('author_id'),
      'user_id': $profile.data('user_id'),
      'defendant_id': $profile.data('defendant_id')
    };

    return data;
  }

  function process_request(profileData){
    $.post(pprsus_settings.pprsus_ajaxurl, profileData, function(response){
      $('#user_name').html(response.data);
    });
  }
});