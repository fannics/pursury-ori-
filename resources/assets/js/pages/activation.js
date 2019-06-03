$(function(){

    var currentRequest = null;

    $(document).on('click', '#a', function(e){
        e.preventDefault();

        var that = this;
        var currentText = $(this).text();

        if (currentRequest)
            currentRequest.abort();

        $(this).text($(this).attr('data-loading'));

        currentRequest = $.post($(this).attr('data-url'), {'key': $(this).attr('data-token')})
            .success(function(res){

                var message = '';
                var activationEmailError = (typeof global_activationEmailError === 'undefined') ? 'An error has happened when trying to send the activation e-mail. Our administrators have been notified about this. Try again.' : global_activationEmailError;
                var sentEmail = (typeof global_sentEmail === 'undefined') ? 'E-mail has been sent' : global_sentEmail;

                if (res.message != undefined){
                    message = res.message;
                } else {
                    message = activationEmailError
                }

                bootbox.dialog({
                    message: message,
                    title: sentEmail,                                   
                    buttons: {
                        success: {
                            label: "OK",
                            className: res.status == 'success' ? "btn-success" : 'btn-danger',
                        }
                    }
                });
            })
            .error(function() {
            
                var activationEmailError = (typeof global_activationEmailError === 'undefined') ? 'An error has happened when trying to send the activation e-mail. Our administrators have been notified about this. Try again.' : global_activationEmailError;
                var activationEmailErrorTitle = (typeof global_activationEmailErrorTitle === 'undefined') ? 'Oops. An error has happened' : global_activationEmailErrorTitle;                
            
                bootbox.dialog({
                    message: activationEmailError,
                    title: activationEmailErrorTitle,
                    buttons: {
                        success: {
                            label: "OK",
                            className: "btn-danger",
                        }
                    }
                });
            })
            .done(function(){
                currentRequest = null;
                $(that).text(currentText);
            });
    });
});