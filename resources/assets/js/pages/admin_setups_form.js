$(function(){
    $('#setup-form').validate({
        rules: {
            setup_country: {
                required: true,
            },
            setup_country_abre: {
                required: true,
            },
            setup_language: {
                required: true,
            },
            setup_language_abre: {
                required: true,
            },
            setup_currency: {
                required: true,
            },
            setup_currency_symbol: {
                required: true,
            },
            setup_before_after: {
                required: true,
            },
            setup_currency_decimal: {
                required: true,
            },
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