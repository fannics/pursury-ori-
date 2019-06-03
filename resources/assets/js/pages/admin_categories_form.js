$(function(){
    $('#category-form').validate({
        rules: {
            category_title: {
                required: true,
            },
            category_url: {
                required: true,
            },
            category_meta_title: {
                required: true,
            },
            category_sorting: 'required',
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