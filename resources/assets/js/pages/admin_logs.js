$(function(){
    $('.logs-table-container').on('click', '.expand', function(){
        $('#' + $(this).attr('data-display')).toggle();
    });
});
