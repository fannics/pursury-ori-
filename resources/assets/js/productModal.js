(function($){

    $.fn.productModal = function(){

        return $(this).each(function(){

            var elem = $(this);

            var $modal = null;

            var $similar_products_html = null;

            var isMobile = $('#mobile-selector').first().css('display') == 'none';

            var cloneSimilarProductTemplate = function(data){
console.log(data);
                var html = $similar_products_html;

                html = html.replace(/%sim_product_title%/gi, data.title);

                html = html.replace(/data-sim_product_href=""/gi, 'href="'+data.url_key+'"');

                if (data.previous_price !== null && data.previous_price && data.previous_price !== 'null'){
                    html = html.replace(/%sim_product_previous_price%/, data.previous_price);
                } else {
                    html = html.replace(/%sim_product_previous_price%/, '');
                }

                html = html.replace(/%sim_product_price%/gi, data.price);

                html = html.replace(/%sim_product_discount%/gi, data.discount);

                html = html.replace(/%sim_product_image_url%/gi, data.thumbnail);

                html = html.replace(/%sim_product_id%/gi, data.id);

                html = $(html);

                return html;

            };

            var fetchData = function(isMobile){
                var d = $.Deferred();

                $.get(elem.attr('data-product-url'), {mob: isMobile})
                    .success(function(res){
                        if (res.status == 'success'){
                            d.resolve(res);
                        } else {
                            d.reject(res);
                        }
                    })
                    .fail(function(){
                        d.reject(arguments);
                    });

                return d.promise();
            };

            var attachEvents = function(){

                $modal.find('.close-btn').click(function(){

                    if (isMobile){
                        $modal.remove();
                        $('body').removeClass('mobile-product-modal-open').removeClass('product-modal-open');
                    } else {
                        $modal.slideUp('fast', function(){
                            $modal.remove();
                            $('body').removeClass('mobile-product-modal-open').removeClass('product-modal-open');
                        });
                    }
                });

                $(window).on('resize', function(){
                    $('#mobile-selector').first().css('display') == 'none';
                });

                $(document).on('scroll', function(){
                    if ($('body').hasClass('mobile-product-modal-open')){
                        if ($(document).scrollTop() > 100){
                            if(!$modal.hasClass('header-fixed')){
                                $modal.addClass('header-fixed');
                            }
                        } else {
                            $modal.removeClass('header-fixed');
                        }
                    }
                });

            };

            var updateTemplate = function(template, data){

                for(var index in data){
                    var re = new RegExp('%' + index + '%', 'gi');
                    template = template.replace(re, data[index]);
                    /*to not have a error 400 as it was for previous link formats it is moved to data elements*/
                    var urlRe = new RegExp('data-' + index+'=""' , 'gi');
                    var sp = index.split('_');
                    var spL = sp.length;
                    var attr = sp[spL-1];
                    if(attr == "href" || attr == "src"){
                        template = template.replace(urlRe, attr+'="'+data[index]+'"');
                    }

                }
                return template;


            };

            var init = function(){

                $('.product-tile-wrapper.active').removeClass('active');

                elem.addClass('active');

                var $parent = elem.parent();

                if ($('.product-modal.open').size() > 0){

                    $('.product-modal.open').slideUp('fast', function(){
                        $(this).remove()
                    });

                    $('body').removeClass('mobile-product-modal-open').removeClass('product-modal-open');
                }

                var width = elem.width();
                var position = elem.position();
                var index = Math.ceil(position.left / parseInt(width));
                var elemCount = Math.ceil($parent.width() / width) - 1;

                var lastElem = elem;

                for(var i = 0; i < elemCount - index; i++){

                    var next = lastElem.next('.product-tile-wrapper');

                    if (next.size() > 0){
                        lastElem = next;
                    }
                }

                var data = {
                    product_title: elem.attr('data-product-title'),
                    product_href: elem.attr('data-product-url'),
                    product_price: elem.attr('data-product-price'),
                    product_previous_price: elem.attr('data-product-pprice'),
                    product_id: elem.attr('data-product-id'),
                    product_image_src: elem.attr('data-image-url')
                };

                $modal_html = updateTemplate($('#product-modal').html(), data);

                $modal =  $($modal_html);

                if (elem.attr('data-on-wishlist') == 'true'){
                    $modal.find('.wishlist').addClass('on-wishlist');
                } else {
                    $modal.find('.wishlist').removeClass('on-wishlist');
                }

                $similar_products_html = $modal.find('#similar-product-template').html();

                $modal.addClass('open');

                if (isMobile){

                    //is mobile
                    $('body').append($modal);
                    $('body').addClass('mobile-product-modal-open');

                    $modal.show();

                    $('body').scrollTop(0);

                } else {
                    //is desktop

                    $modal.insertAfter(lastElem);

                    $('body').addClass('product-modal-open');

                    $modal.slideDown('fast', function(){
                        $modal.animatedScroll({
                            duration: 'normal',
                            easing: 'linear'
                        });
                    });
                }

                fetchData(isMobile).then(function(res){

                    var data = res.data;

                    $modal.find('img').replaceWith('<img src="' + data.image_url + '" alt="' + data.product_title + '"/>');

                    if (undefined !== res.similar_products){

                        $modal.find('.similar-products').show();

                        for(var i in res.similar_products){

                            var sim_obj = cloneSimilarProductTemplate(res.similar_products[i]);

                            $modal.find('.similar-products .products-wrapper').append(sim_obj);

                        }

                    }

                }, function(){

                });

                attachEvents();

            };

            init();

        });
    };

})(jQuery);