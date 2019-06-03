$(function(){

    $('#product-category').select2();
    
    var productTitleMinLength = (typeof global_productTitleMinLength === 'undefined') ? 'Product title must have at least 4 characters' : global_productTitleMinLength;
    var productDestinationURLURL = (typeof global_productDestinationURLURL === 'undefined') ? 'You must enter a valid URL' : global_productDestinationURLURL;
    var productPriceNumber = (typeof global_productPriceNumber === 'undefined') ? 'You must enter a valid number' : global_productPriceNumber; 
    var productPriceMin = (typeof global_productPriceMin === 'undefined') ? 'You must enter a number greater than 0' : global_productPriceMin;
    var productPreviousPriceNumber = (typeof global_productPreviousPriceNumber === 'undefined') ? 'You must enter a valid number' : global_productPreviousPriceNumber;
    var productPreviousPriceMin = (typeof global_productPreviousPriceMin === 'undefined') ? 'You must enter a number greater than 0' : global_productPreviousPriceMin;
    var productCouponURLURL = (typeof global_productCouponURLURL === 'undefined') ? 'You must enter a valid URL' : global_productCouponURLURL;    

    $('#product-form').validate({
        rules: {
            product_title: {
                required: true,
                minlength: 4
            },
            product_id: 'required',
            product_category: 'required',
            product_url: 'required',
            product_destination_url: {
                required: true,
                url: true
            },
            product_price: {
                required: true,
                number: true,
                min: 0
            },
            product_previous_price: {
                number: true,
                min: 0
            },
            product_meta_title: 'required',
            product_meta_description: 'required'
        },
        messages: {
            product_title: {
                minlength: productTitleMinLength
            },
            product_destination_url: {
                url: productDestinationURLURL
            },
            product_price: {
                number: productPriceNumber,
                min: productPriceMin
            },
            product_previous_price: {
                number: productPreviousPriceNumber,
                min: productPreviousPriceMin
            }
        }
    });
});