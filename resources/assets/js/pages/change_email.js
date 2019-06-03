$(function() {

    var newEmailRequired = (typeof global_newEmailRequired === 'undefined') ? 'This field is mandatory' : global_newEmailRequired;
    var newEmailEmail = (typeof global_newEmailEmail === 'undefined') ? 'You must to enter a valid e-mail' : global_newEmailEmail;
    var newEmailConfRequired = (typeof global_newEmailConfRequired === 'undefined') ? 'This field is mandatory' : global_newEmailConfRequired;
    var newEmailConfEmail = (typeof global_newEmailConfEmail === 'undefined') ? 'You must to enter a valid e-mail' : global_newEmailConfEmail;
    var newEmailConfEqualTo = (typeof global_newEmailConfEqualTo === 'undefined') ? 'E-mail addresses must match' : global_newEmailConfEqualTo;    
 
    $('#change-email').validate({
        rules: {
            new_email: {
                required: true,
                email: true
            },
            new_email_conf: {
                required: true,
                equalTo: 'input[name=new_email]'
            }
        },
        messages: {
            new_email: {
                required: newEmailRequired,
                email: newEmailEmail
            },
            new_email_conf: {
                required: newEmailConfRequired,
                email: newEmailConfEmail,
                equalTo: newEmailConfEqualTo
            }
        }
    });
});