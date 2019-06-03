$(document).on('click', '#send-mail-btn', function(e){
    e.preventDefault();
    var alertContainer = $('#test-email-form .alert').first();
    alertContainer.attr('class', 'alert');
    alertContainer.empty();
    alertContainer.hide();
    $.post($('#test-email-form').attr('action'), {email: $('#email_address').val(), template: $('#email_template').val()})
        .success(function(res){
            if (res.status == 'not_valid'){

                alertContainer.addClass('alert-danger');

                for (var i in res.messages){
                    alertContainer.append('<p>' + res.messages[i] + '</p>')
                }

                alertContainer.show();

                return;
            }
            if (res.status == 'success'){
                alertContainer.addClass('alert-success');
                
                var testEmailSent = (typeof global_testEmailSent === 'undefined') ? 'A test e-mail has been sent successfully' : global_testEmailSent;

                alertContainer.append('<p>' + testEmailSent + '</p>');

                alertContainer.show();
                return;
            }
        })
        .fail(function(){
            alert('error');
        });
});