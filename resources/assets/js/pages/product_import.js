$(function(){

    if ($('.product-import').size() > 0){

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

            var import_id = null;

            resetData(elem);

            var importingProducts = (typeof global_importingProducts === 'undefined') ? 'Importing products' : global_importingProducts;
            setMessage(elem, global_importingProducts);

            $.post(window.app_prefix + '/admin/products/import', {
                file: file
            })
            .success(function(res){

                if (res.status == 'success'){

                    $('.import-results').html(res.results);

                    import_id = res.import_id;

                    setPersistentMessage(elem, '<p class="success">' + res.message + '</p>');
                    
                    var updatingAppRoutes = (typeof global_updatingAppRoutes === 'undefined') ? 'Updating application routes' : global_updatingAppRoutes;
                    setMessage(elem, updatingAppRoutes);

                    $.post(window.app_prefix + '/admin/products/update_routes', {import_id: import_id})
                .success(function(res){

                        if (res.status == 'success'){

                            var updatingSearchEngine = (typeof global_updatingSearchEngine === 'undefined') ? 'Updating search engine' : global_updatingSearchEngine;
                            setMessage(elem, updatingSearchEngine);
                            setPersistentMessage(elem, '<p class="success">' + res.message + '</p>');

                            $.post(window.app_prefix + '/admin/search-engine-refresh', {import_id: import_id})
                                .success(function(res){
                                    if (res.status == 'success'){
                                        setPersistentMessage(elem, '<p class="success">' + res.message + '</p>');
                                        var actionhasFinished = (typeof global_actionhasFinished === 'undefined') ? 'Action has finished' : global_actionhasFinished;
                                        setCompleted(elem, actionhasFinished);

                                    } else {

                                        setPersistentMessage(elem, '<p class="error">' + res.message + '</p>');

                                    }
                                })
                                .fail(function(){

                                    var errorUpdatingIndex = (typeof global_errorUpdatingIndex === 'undefined') ? 'An error has happened when trying to update the search index' : global_errorUpdatingIndex;
                                    setPersistentMessage(elem, '<p class="error">' + errorUpdatingIndex + '</p>');

                                });

                        } else {

                            setPersistentMessage(elem, '<p class="error">' + res.message + '</p>');

                        }

                    })
                        .fail(function(){

                            var errorUpdatingRoutes = (typeof global_errorUpdatingRoutes === 'undefined') ? 'An error has happened when trying to update the application routes' : global_errorUpdatingRoutes;
                            setPersistentMessage(elem, '<p class="error">' + errorUpdatingRoutes + '</p>');

                        });

                } else {

                    setPersistentMessage(elem, '<p class="error">' + res.message + '</p>');

                }

            })
                .fail(function(){
                    var errorImportingFile = (typeof global_errorImportingFile === 'undefined') ? 'An error has happened when trying to import the file. Try again.' : global_errorImportingFile;
                    setPersistentMessage(elem, '<p class="error">' + errorImportingFile + '</p>');
                });
        });

        $('#loading-modal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });

        $('#fileupload').fileupload({
            url: window.app_prefix + '/admin/products/upload',
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