$(function(){
    $('#password-form').validate({
        rules: {
            email: {
                required: true,
                email: true
            }
        }
    });
});