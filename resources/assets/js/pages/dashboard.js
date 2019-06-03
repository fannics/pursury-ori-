$(function(){

    $(document).on('click', '.global-index-action', function(e){
        e.preventDefault();

        var that = this;

        bootbox.confirm($(that).attr('data-confirm-message'), function(res){
            if (res == true){

                var icon = $(that).find('i');

                icon.removeClass(icon.attr('data-regular-icon'));

                icon.addClass('fa-spinner fa-spin');

                $.post($(that).attr('href'))
                    .success(function(res){
                        bootbox.alert(res.message);
                    })
                    .fail(function(){
                        var errorHappened = (typeof global_errorHappened === 'undefined') ? 'There was an error when trying to execute the required action' : global_errorHappened;
                        bootbox.alert(errorHappened);
                    })
                    .done(function(){
                        icon.removeClass('fa-spinner fa-spin');
                        icon.addClass(icon.attr('data-regular-icon'));
                    });
            }
        });

    });

});