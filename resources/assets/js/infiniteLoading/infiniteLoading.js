(function(){

    $.fn.infiniteLoading = function(options, params){

        return $(this).each(function(){

            var that = this;

            var api = {
                setMessage: function(message){
                    $(that).closest('.modal-body').find('.message-wrapper').text(message);
                },
                setPersistentMessage: function(message){
                    $(that).closest('.modal-body').find('.persistent-messages').append(message);
                },
                setCompleted: function(){
                    $(that).prop('completed', true);
                    $(that).find('.progress-bar').addClass('progress-bar-success');
                    $(that).closest('.modal-body').find('.close-button-wrapper').show();
                },
                resetData: function(){
                    $(that).closest('.modal-body').find('.message-wrapper').empty();
                    $(that).closest('.modal-body').find('.persistent-messages').empty();
                    $(that).closest('.modal-body').find('.close-button-wrapper').hide();
                    $(that).find('.progress-bar').removeClass('progress-bar-success');
                }
            };

            var init = function(){
                $(that).addClass('il');

            };

            if (!$(that).hasClass('il')){

                init();
            } else {
                if (options !== undefined){
                    api[options](params);
                }
            }

        });
    };
})(jQuery);