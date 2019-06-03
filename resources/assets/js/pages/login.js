$(function(){
    $('#login-form').validate({
        rules: {
            email: {
                email: true,
                required: true
            },
            password: {
                required: true
            }
        }
    });
});
