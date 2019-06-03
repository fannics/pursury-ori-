$(function(){

    if ($('.category-import').size() > 0){
        var file = null;

        var setMessage = function(elem, message){
            $(elem).closest('.modal-body').find('.message-wrapper').text(message);
        };

        var setPersistentMessage = function(elem, message){
            $(elem).closest('.modal-body').find('.persistent-messages').append(message);
        };

        var setCompleted = function(elem, message){
            $(elem).prop('completed', true);
            $(elem).find('.progress-bar').addClass('progress-bar-success');
            $(elem).closest('.modal-body').find('.close-button-wrapper').show();
            setMessage(elem, message);
        };

        var resetData = function(elem){
            $(elem).closest('.modal-body').find('.message-wrapper').empty();
            $(elem).closest('.modal-body').find('.persistent-messages').empty();
            $(elem).closest('.modal-body').find('.close-button-wrapper').hide();
            $(elem).find('.progress-bar').removeClass('progress-bar-success');
        };

        $('#loading-modal').on('shown.bs.modal', function (e) {

            var elem = $('.infinite-progress');

            resetData(elem);
            
            var importingCategories = (typeof global_importingCategories === 'undefined') ? 'Importing categories' : global_importingCategories;
            setMessage(elem, importingCategories);

            $.post(window.app_prefix + '/admin/categories/import', {
                    file: file
                })
                .success(function(res){

                    if (res.status == 'success'){

                        $('.import-results').html(res.results);

                        setPersistentMessage(elem, '<p class="success">' + res.message + '</p>');

                        var updatingAppRoutes = (typeof global_updatingAppRoutes === 'undefined') ? 'Updating application routes' : global_updatingAppRoutes;
                        setMessage(elem, updatingAppRoutes);

                        $.post(window.app_prefix + '/admin/products/update_categories_routes')
                            .success(function(res){

                                if (res.status == 'success'){

                                    var updatingCategoryTree = (typeof global_updatingCategoryTree === 'undefined') ? 'Updating categories tree' : global_updatingCategoryTreee;
                                    setMessage(elem, updatingCategoryTree);
                                    setPersistentMessage(elem, '<p class="success">' + res.message + '</p>');

                                    $.post(window.app_prefix + '/admin/products/update_category_tree')
                                        .success(function(res){
                                            if (res.status == 'success'){
                                                setPersistentMessage(elem, '<p class="success">' + res.message + '</p>');
                                                var actionhasFinished = (typeof global_actionhasFinished === 'undefined') ? 'Action has finished' : global_actionhasFinished;
                                                setCompleted(elem, actionhasFinished);
                                            } else {
                                                setPersistentMessage(elem, '<p class="error">' + res.message + '</p>');
                                                var actionNotDone = (typeof global_actionNotDone === 'undefined') ? 'Action has not been done' : global_actionNotDone;
                                                setCompleted(elem, actionNotDone);
                                            }
                                        })
                                        .fail(function(){
                                            var errorUpdatingCategoriesTree = (typeof global_errorUpdatingCategoriesTree === 'undefined') ? 'There was an error when trying to update the categories tree' : global_errorUpdatingCategoriesTree;
                                            setPersistentMessage(elem, '<p class="error">' + errorUpdatingCategoriesTree + '</p>');
                                            var actionNotDone = (typeof global_actionNotDone === 'undefined') ? 'Action has not been done' : global_actionNotDone;
                                            setCompleted(elem, actionNotDone);
                                        });

                                } else {

                                    setPersistentMessage(elem, '<p class="error">' + res.message + '</p>');
                                    var actionNotDone = (typeof global_actionNotDone === 'undefined') ? 'Action has not been done' : global_actionNotDone;
                                    setCompleted(elem, actionNotDone);

                                }

                            })
                            .fail(function(){

                                var errorUpdatingRoutes = (typeof global_errorUpdatingRoutes === 'undefined') ? 'An error has happened when trying to update the application routes' : global_errorUpdatingRoutes;
                                setPersistentMessage(elem, '<p class="error">' + errorUpdatingRoutes + '</p>');
                                var actionNotDone = (typeof global_actionNotDone === 'undefined') ? 'Action has not been done' : global_actionNotDone;
                                setCompleted(elem, actionNotDone);

                            });

                    } else {

                        setPersistentMessage(elem, '<p class="error">' + res.message + '</p>');
                        var actionNotDone = (typeof global_actionNotDone === 'undefined') ? 'Action has not been done' : global_actionNotDone;
                        setCompleted(elem, actionNotDone);

                    }

                })
                .fail(function(){

                    var errorImportingFile = (typeof global_errorImportingFile === 'undefined') ? 'An error has happened when trying to import the file. Try again.' : global_errorImportingFile;
                    setPersistentMessage(elem, '<p class="error">' + errorImportingFile + '</p>');
                    var actionNotDone = (typeof global_actionNotDone === 'undefined') ? 'Action has not been done' : global_actionNotDone;
                    setCompleted(elem, actionNotDone);

                });                                                                                      
        });

        $('#loading-modal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });


        $('#fileupload').fileupload({
            url: window.app_prefix + '/admin/categories/upload',
            dataType: 'json',
            send: function(e, data){
                var elem = $('#upload-btn > span');
                var text = elem.text();
                elem.text(elem.attr('data-loading-text'));
                elem.attr('data-loading-text', text);
                $('.import-results').empty();
            },
            done: function (e, data) {
                var result = data.result;
                if (result.status == 'success'){
                    file = result.filename;
                    $('#loading-modal').modal('show');
                } else {
                    var errorUploadingFile = (typeof global_errorUploadingFile === 'undefined') ? 'An error has happened when trying to upload the file. Try again.' : global_errorUploadingFile;
                    bootbox.alert(errorUploadingFile);
                }
            },
            always: function(e, data){
                var elem = $('#upload-btn > span');
                var text = elem.text();
                elem.text(elem.attr('data-loading-text'));
                elem.attr('data-loading-text', text);
            }
        }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');

    }
});