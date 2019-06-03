$(function(){

    $(document).on('ifChanged', '.image-engine-selector', function(){
        if ($(this).val() == 'thumbor'){
            $('.thumbor-url').slideDown();
        } else {
            $('.thumbor-url').slideUp();
        }
    });

    $('#settings-form').validate({
        rules: {
            'app_title': 'required',
            'currency_name': 'required',
            'currency_html_code': 'required',
            'money_decimal_digits': 'required',
            'money_decimal_separator': 'required',
            'money_thousands_separator': 'required',
            'product_order_limit': {
                required: true,
                number: true
            }
        }
    });

});
