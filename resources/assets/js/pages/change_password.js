$(function () {

    var newPasswordRequired = (typeof global_newPasswordRequired === 'undefined') ? 'This field is mandatory' : global_newPasswordRequired;
    var newPasswordMinLength = (typeof global_newPasswordMinLength === 'undefined') ? 'Password must have at least 6 characters' : global_newPasswordMinLength;    
    var newPasswordConfRequired = (typeof global_newPasswordConfRequired === 'undefined') ? 'This field is mandatory' : global_newPasswordConfRequired;
    var newPasswordConfEqualTo = (typeof global_newPasswordConfEqualTo === 'undefined') ? 'Passwords must match' : global_newPasswordConfEqualTo;    

    $('#change-password-form').validate({
        rules: {
            new_password: {
                required: true,
                minlength: 6
            },
            new_password_conf: {
                required: true,
                equalTo: 'input[name=new_password]'
            }
        },
        messages: {
            new_password: {
                required: newPasswordRequired,
                minlength: newPasswordMinLength
            },
            new_password_conf: {
                required: newPasswordConfRequired,
                equalTo: newPasswordConfEqualTo
            }
        }
    });
});