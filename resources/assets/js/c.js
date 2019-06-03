$(function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ajaxError(function(event, request, settings){
        switch(request.status){
            case 401:

                $('#login-required-modal').modal({
                    backdrop: 'static',
                    keyboard: true,
                    show: true
                });

                break;
        }
    });

    $(".nav .dropdown-toggle").click(function () {
        window.location = this.href;
    });

    $('input[type=checkbox], input[type=radio]').each(function(){
        if ($(this).hasClass('small-icheck')){

        } else {
            $(this).iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '0'
            });
        }
    });

    //async load of images for the product tiles

    var handleVisibilityChange = function(){
        $('.product-tile .image-async-loader').each(function(){
            if ($(this).parent().parent().visible(true, true)){

                var src = $(this).attr('data-src');
                var alt = $(this).attr('data-alt');

                var parent = $(this).closest('.product-image');

                //$(this).closest('.product-image').addClass('no-back');
                $(this).replaceWith('<img src="' + src + '" alt="' + alt + '"/>');

                $(parent).find('img').one("load", function() {
                    parent.addClass('no-back');
                }).each(function() {
                    if(this.complete) $(this).load();
                });
            }
        });
    };

    $(document).on('scroll', function(){
        handleVisibilityChange();
    });

    $(document).on('resize', function(){
        handleVisibilityChange();
    });

    handleVisibilityChange();

    // animation added to the product tile hover overlay
    // $(document).on('mouseenter', '.product-tile .overlay a', function(){
    //     if (!$(this).closest('.product-tile').hasClass('wishlist-tile')){
    //         $(this).addClass('animated infinite pulse');
    //     }
    // });
    //
    // $(document).on('mouseleave', '.product-tile .overlay a', function(){
    //     if (!$(this).closest('.product-tile').hasClass('wishlist-tile')){
    //         $(this).removeClass('animated infinite pulse');
    //     }
    // });

    var handleClickOnSimilarProduct = function(elem){
        $(elem).find('form').submit();
    };

    $(document).on('click', '.product-tile a', function(e){
        e.stopPropagation();
        e.preventDefault();
        if (!$(this).is('.wishlist')){
            if ($('.similar-products').has($(this)).length == 0){
                $(this).closest('.product-tile-wrapper').productModal();
            } else {
                handleClickOnSimilarProduct($(this).closest('.product-tile-wrapper'));
            }

        }
    });

    $(document).on('click', '.product-tile', function(){
        if ($('.similar-products').has($(this)).length == 0){
            $(this).closest('.product-tile-wrapper').productModal();
        } else {
            handleClickOnSimilarProduct($(this).closest('.product-tile-wrapper'));
        }
    });

    var updateIfInModal = function(elem, status){
        if (elem.closest('.product-modal').size() > 0){
            // find the product tile wrapper

            var wrapper = $('.product-tile-wrapper.active').first();

            if (wrapper){

                var button = wrapper.find('.wishlist').first();

                console.log(button);

                if (status == true){
                    button.removeClass('prim-btn');
                    button.addClass('on-wishlist def-btn');
                    wrapper.attr('data-on-wishlist', 'true')
                } else {
                    button.removeClass('on-wishlist');
                    button.removeClass('def-btn');
                    button.addClass('prim-btn');
                    wrapper.attr('data-on-wishlist', 'false')
                }

            }

        }
    };


    //wishlist behaviour
    $(document).on('click', '.wishlist', function(e){
        e.preventDefault();
        e.stopPropagation();
        var that = this;
        $.post($(this).attr('data-url'), {p: $(this).attr('data-pi')})
            .success(function(res){
                if (res.message == 'added'){
                    $(that).removeClass('prim-btn');
                    $(that).addClass('on-wishlist def-btn');
                    updateIfInModal($(that), true);
                } else {
                    $(that).removeClass('on-wishlist');
                    $(that).removeClass('def-btn');
                    $(that).addClass('prim-btn');
                    updateIfInModal($(that), false);
                }

                if ($(that).hasClass('remove_on_toggled')){
                    $(that).closest('.product-tile-wrapper').remove();

                    //search for products on wishlist if in profile page to show
                    // the no products message after last product removal
                    if ($('.profile-wishlist-wrapper').size() > 0){
                        if ($('.profile-wishlist-wrapper .product-tile-wrapper').size() == '0'){
                            $('.profile-wishlist-wrapper').find('p').show();
                        }
                    }

                }
            })
            .error(function(data, textStatus, errorThrown){
            });
    });

    //global alert showing
    $('.sysalert').each(function(){
        //bootbox.alert($(this).text());

        var that = this;

        if ($(this).attr('data-type') == 'success') {
            var alertTitle = (typeof $(this).attr('alert-title') === 'undefined') ? 'Alert' : $(this).attr('alert-title');
            bootbox.dialog({
                message: $(that).text(),
                title: alertTitle,
                buttons: {
                    success: {
                        label: "OK",
                        className: "btn-success",
                        callback: function() {

                        }
                    }
                }
            });
            return;
        }

        if ($(this).attr('data-type') == 'error') {
            var alertTitle = (typeof $(this).attr('alert-title') === 'undefined') ? 'Alert' : $(this).attr('alert-title');
            bootbox.dialog({
                message: $(that).text(),
                title: alertTitle,
                buttons: {
                    success: {
                        label: "OK",
                        className: "btn-danger",
                        callback: function() {

                        }
                    }
                }
            });
            return;
        }

    });

    if ($(window).height() > $('body').height()){
        $('#main-footer').addClass('fixed');
    }

    $('.sns').each(function(){
        $(this).snsShare($(this).attr('data-sns-title'), $(this).attr('data-sns-url'));
    });

    $('.see-more-filter-list').each(function(){
        $(this).seeMoreFilters({
            initial: 4,
            moreLabel: '+ Ver más'
        })
    });
});