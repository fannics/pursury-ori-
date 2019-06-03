$(function() {

    var accountAlreadyExists = (typeof global_accountAlreadyExists === 'undefined') ? 'There is already an account registered with that address in our site' : global_accountAlreadyExists;
    var passwordsMustMatch = (typeof global_passwordsMustMatch === 'undefined') ? 'Passwords must to match' : global_passwordsMustMatch;

    $('#register-form').validate({
        ignore: '',
        rules: {
            name: 'required',
            email: {
                required: true,
                email: true,
                remote: $('input[name=email]').attr('data-remote-checker')
            },
            password: {
                required: true
            },
            password_confirmation: {
                required: true,
                equalTo: 'input[name=password]'
            },
            gender: {
                required: true
            }
        },
        messages: {
            email: {
                remote: accountAlreadyExists
            },
            password_confirmation: {
                equalTo: passwordsMustMatch
            }
        },
        errorPlacement: function(error, element){
            if ($(element).is('input:checkbox') || $(element).is('input:radio')){
                error.appendTo(element.closest('.form-group'));
            } else {
                error.appendTo(element.parent());
            }
        }
    });
});
