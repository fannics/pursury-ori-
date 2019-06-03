$(function(){

    $(document).on('click', 'a.global-action', function(e){

        e.preventDefault();
        e.stopPropagation();

        var $link = $(this);

        var postData = {
            type: $link.attr('data-otype'),
            id: $link.attr('data-oid'),
            action: $link.attr('data-action')
        };

        var performAction = function(){
            $.post(window.app_prefix + '/admin/global-action', postData)
                .success(function(res){

                    if ($link.closest('.dataTable').size() > 0){
                        $link.closest('.dataTable').dataTable().api().ajax.reload(function(){
                            bootbox.alert(res.message);
                        }, false);
                    }

                })
                .fail(function(){
                    var errowWhenDoingAction = (typeof global_errowWhenDoingAction === 'undefined') ? 'An error has happened when trying to do this action.' : global_errowWhenDoingAction;
                    bootbox.alert(errowWhenDoingAction);
                });
        };


        if ($link.attr('data-confirm')){
            bootbox.confirm($link.attr('data-confirm'), function(res){
                if (res == true){
                    performAction();
                }
            });
        } else {
            performAction();
        }
    });

});