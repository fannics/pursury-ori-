(function ( $ ) {

    $.fn.searchWidget = function( options ) {

        var that = this;
        var current_term = '';
        var runningRequest = null;
        var resultsPlaceholder = null;
        var moreResults = null;
        var settings = $.extend({

        }, options );

        return this.on('keyup', function(){
            if ($(this).val() && $(this).val() != current_term){

                current_term = $(this).val();

                if (runningRequest){
                    runningRequest.abort();
                }

                var url = settings.url($(this).val());

                runningRequest = $.get(url)
                    .success(function(res){

                        if (settings.resultsPlaceholder && !resultsPlaceholder ){

                            resultsPlaceholder = $(settings.resultsPlaceholder);

                        }

                        if (resultsPlaceholder){

                            $(resultsPlaceholder.empty());
                            
                            if (resultsPlaceholder.attr('data-autoshow')){
                                resultsPlaceholder.show();
                            }

                            if (res.length > 0){
                                $.each(res, function(){
                                    $(resultsPlaceholder).append(settings.resultTemplate(this));
                                });
                                moreResults = $(options.moreResultsMessage);

                            } else {
                                if (current_term){
                                    $(resultsPlaceholder).append(settings.noResultErrorMessage);
                                }
                            }
                        }
                    })
                    .fail(function(){

                    });
            }
        });
    };

}( jQuery ));