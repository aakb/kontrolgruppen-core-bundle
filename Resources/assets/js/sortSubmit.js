$(document).ready(function(){
  // Attach sort direction and submit on change
  $('form select').on('change', function () {
    $('.js-process-table-results').hide();
    $('#js-process-spinner').show();

    let sortDirection = $(this).closest('form').find('#js-sort-select').val();

    console.log(sortDirection);

    $(this).closest('form').append('<input type="hidden" name="sort_direction" value="'+sortDirection+'"/>');

    $(this).closest('form').submit();
  });
});
