(function ( $ ) {

    $.fn.searchWidget = function( options ) {

        var that = this;
        var current_term = '';
        var runningRequest = null;
        var resultsPlaceholder = null;
        var moreResults = null;
        var settings = $.extend({

        }, options );

        return this.on('keyup', function(){
            if ($(this).val() && $(this).val() != current_term){

                current_term = $(this).val();

                if (runningRequest){
                    runningRequest.abort();
                }

                var url = settings.url($(this).val());

                runningRequest = $.get(url)
                    .success(function(res){

                        if (settings.resultsPlaceholder && !resultsPlaceholder ){

                            resultsPlaceholder = $(settings.resultsPlaceholder);

                        }

                        if (resultsPlaceholder){

                            $(resultsPlaceholder.empty());
                            
                            if (resultsPlaceholder.attr('data-autoshow')){
                                resultsPlaceholder.show();
                            }

                            if (res.length > 0){
                                $.each(res, function(){
                                    $(resultsPlaceholder).append(settings.resultTemplate(this));
                                });
                                moreResults = $(options.moreResultsMessage);

                            } else {
                                if (current_term){
                                    $(resultsPlaceholder).append(settings.noResultErrorMessage);
                                }
                            }
                        }
                    })
                    .fail(function(){

                    });
            }
        });
    };

}( jQuery ));
(function(){
    $.fn.seeMoreFilters = function(options){

        return $(this).each(function(){

            var that = this;

            var itemCount = $(that).find('li').length;

            var initial = parseInt(options.initial);

            var moreElementTemplate = '<li><a href="#" class="filter-list-more-link">' + options.moreLabel + '</li>';

            var expand = function(){

            };

            var init = function(){

                //initially hide the extra elements

                var expand = options.expand !== undefined ? options.expand : false;

                if (initial < itemCount && !expand){
                    $(that).find('li').slice(parseInt(options.initial), itemCount).hide();
                    $(that).append(moreElementTemplate)

                    $(that).on('click', '.filter-list-more-link', function(e){
                        e.preventDefault();

                        $(that).find('li').slice(parseInt(options.initial), itemCount).fadeIn('fast');

                        $(this).parent().hide();

                    });
                }
            };

            init();

        });

    }
})(jQuery);

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

    $("#optionsSelect").click(
      function () {
        $("#optionsSelect").hide();
        $("#seeAvailableOptions").show();
      }
    );
    
    $(".nav .dropdown-toggle").click(function () {
        window.location = this.href;
    });

    $('input[type=checkbox], input[type=radio]').each(
      function(){
        if ($(this).hasClass('small-icheck')) {
        }
        else {
          $(this).iCheck(
            {
              checkboxClass: 'icheckbox_square-blue',
              radioClass: 'iradio_square-blue',
              increaseArea: '0'
            }
          );
        }
      }
    );

    $('input[name=selectedItem]').on('ifChanged', 
      function(event) {
        var newLabel = $('#item_' + $(this).val()).text();
        var newPrice = $('#price_' + $(this).val()).text(); 
        $('#id_mobileItemSelector').val($(this).val());
        $('#seeAvailableOptions').hide();
        $('#optionsSelect').show();
        $('#availableOptionsLabel').text(newLabel);
        $('#id_selectedPrice').text(newPrice);
        $('#mobileChildrePurchaseButton').show();
      }
    );

    var handleVisibilityChange = function() {
      $('.product-tile .image-async-loader').each(
        function() {
          if ($(this).parent().parent().visible(true, true)) {
            var src = $(this).attr('data-src');
            var alt = $(this).attr('data-alt');
            var parent = $(this).closest('.product-image');
            $(this).replaceWith('<img src="' + src + '" alt="' + alt + '"/>');
            $(parent).find('img').one("load", 
              function() {
                parent.addClass('no-back');
              }
            ).each(
              function() {
                if(this.complete) $(this).load();
              }
            );
          }
        }
      );
    };

    $(document).on('scroll', function(){
        handleVisibilityChange();
    });

    $(document).on('resize', function(){
        handleVisibilityChange();
    });

    handleVisibilityChange();

    var handleClickOnSimilarProduct = function(elem){
        $(elem).find('form').submit();
    };

    $(document).on('click', '.product-tile a', 
      function(e) {
        e.stopPropagation();
        e.preventDefault();
        if (!$(this).is('.wishlist')) {
          if ($('.similar-products').has($(this)).length == 0){
            $(this).closest('.product-tile-wrapper').productModal();
          } 
          else {
            handleClickOnSimilarProduct($(this).closest('.product-tile-wrapper'));
          }
        }
      }
    );

    $(document).on('click', '.product-tile', 
      function() {
        if ($('.similar-products').has($(this)).length == 0) {
          $(this).closest('.product-tile-wrapper').productModal();
        } 
        else {
          handleClickOnSimilarProduct($(this).closest('.product-tile-wrapper'));
        }
      }
    );

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

        if ($(this).attr('data-type') == 'error'){
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

var noResults = (typeof global_noResults === 'undefined') ? "We're sorry, we cannot to find results for the terms" : global_noResults;
var showMoreResults = (typeof global_showMoreResults === 'undefined') ? 'Show more results' : global_showMoreResults;

$('#offcanvas-search-widget').searchWidget({
    url: function(term) {
        var url = $('#offcanvas-search-widget').attr('data-url');
        return url.replace('the_term', term);
    },
    resultsPlaceholder: '.sidebar-results',
    noResultErrorMessage: '<p class="text-center">' + noResults + '</p>',
    moreResultsMessage: '<div class="results-footer"><a href="#">' + showMoreResults + '</a></div>',
    resultTemplate: function(res){
        return '<p><a href="' + res.url + '">' + res.rec_name + '</a></p>';
    }
});

$('#custom-menu-search-widget').searchWidget({
    url: function(term){
        var url = $('#custom-menu-search-widget').attr('data-url');
        return url.replace('the_term', term);
    },
    resultsPlaceholder: '.menu-search-results',
    noResultErrorMessage: '<p class="text-center">' + noResults + '</p>',
    moreResultsMessage: '<div class="results-footer"><a href="#">' + showMoreResults + '</a></div>',
    resultTemplate: function(res){
        return '<a href="' + res.url + '" class="result">' +
            '<div class="result-img">' +
                '<img src="' + res.thumb + '" alt="' + res.rec_name + '" width="50" height="50" alt="' + res.rec_name + '" />' +
            '</div>' +
            '<div class="result-link">' +
                res.rec_name +
            '</div>' +
        '</a>';
    }
});

$(document).on('keyup', '#custom-menu-search-widget', function(){
    if ($(this).val()){
        $('.search-close-btn').show();
    } else {
        $('.search-close-btn').hide();
        $('.menu-search-results').hide();
    }
});
$(document).on('click', '.search-close-btn', function(e){
    e.preventDefault();
    $('#custom-menu-search-widget').val('');
    $('#custom-menu-search-widget').focus();
    $('.menu-search-results').hide();
});

$(document).mouseup(function (e)
{
    var container = $('.menu-search-results');

    if (!container.is(e.target) // if the target of the click isn't the container...
        && container.has(e.target).length === 0) // ... nor a descendant of the container
    {
        container.hide();
    }
});
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
$(function(){

    var newEmailRequired = (typeof global_newEmailRequired === 'undefined') ? 'This field is mandatory' : global_newEmailRequired;
    var newEmailEmail = (typeof global_newEmailEmail === 'undefined') ? 'You must to enter a valid e-mail' : global_newEmailEmail;
    var newEmailConfRequired = (typeof global_newEmailConfRequired === 'undefined') ? 'This field is mandatory' : global_newEmailConfRequired;
    var newEmailConfEmail = (typeof global_newEmailConfEmail === 'undefined') ? 'You must to enter a valid e-mail' : global_newEmailConfEmail;
    var newEmailConfEqualTo = (typeof global_newEmailConfEqualTo === 'undefined') ? 'E-mail addresses must match' : global_newEmailConfEqualTo;    

    $('#change-email').validate({
        rules: {
            new_email: {
                required: true,
                email: true
            },
            new_email_conf: {
                required: true,
                equalTo: 'input[name=new_email]'
            }
        },
        messages: {
            new_email: {
                required: newEmailRequired,
                email: newEmailEmail,
            },
            new_email_conf: {
                required: newEmailConfRequired,
                email: newEmailConfEmail,
                equalTo: newEmailConfEqualTo
            }
        }
    });
});
$(function () {

    var newPasswordRequired = (typeof global_newPasswordRequired === 'undefined') ? 'This field is mandatory' : global_newPasswordRequired;
    var newPasswordMinLength = (typeof global_newPasswordMinLength=== 'undefined') ? 'Password must have at least 6 characters' : global_newPasswordMinLength;    
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
$(function(){

    var currentRequest = null;

    $(document).on('click', '#a', function(e){
        e.preventDefault();

        var that = this;
        var currentText = $(this).text();

        if (currentRequest)
            currentRequest.abort();

        $(this).text($(this).attr('data-loading'));

        currentRequest = $.post($(this).attr('data-url'), {'key': $(this).attr('data-token')})
            .success(function(res){

                var message = '';
                var activationEmailError = (typeof global_activationEmailError === 'undefined') ? 'An error has happened when trying to send the activation e-mail. Our administrators have been notified about this. Try again.' : global_activationEmailError;
                var sentEmail = (typeof global_sentEmail === 'undefined') ? 'E-mail has been sent' : global_sentEmail;
                
                if (res.message != undefined){
                    message = res.message;
                } else {
                    message = activationEmailError
                }

                bootbox.dialog({
                    message: message,
                    title: sentEmail,
                    buttons: {
                        success: {
                            label: "OK",
                            className: res.status == 'success' ? "btn-success" : 'btn-danger',
                        }
                    }
                });
            })
            .error(function(){

                var activationEmailError = (typeof global_activationEmailError === 'undefined') ? 'An error has happened when trying to send the activation e-mail. Our administrators have been notified about this. Try again.' : global_activationEmailError;
                var activationEmailErrorTitle = (typeof global_activationEmailErrorTitle === 'undefined') ? 'Oops. An error has happened' : global_activationEmailErrorTitle;                

                bootbox.dialog({
                    message: activationEmailError,
                    title: activationEmailErrorTitle,
                    buttons: {
                        success: {
                            label: "OK",
                            className: "btn-danger",
                        }
                    }
                });
            })
            .done(function(){
                currentRequest = null;
                $(that).text(currentText);
            });
    });
});
$(function(){

    var siteTitle = (typeof global_siteTitle === 'undefined') ? 'Fannics' : global_siteTitle;
    var searchSite = (typeof global_searchSite === 'undefined') ? 'Buscar en el sitio' : global_searchSite;

    $("#my-menu").mmenu({
        extensions: ["pageshadow", "pagedim-black"],
        navbar: {
            title: siteTitle
        }
    });

    $("#my-menu2").mmenu({
        offCanvas: {
            position: 'right',
        },
        extensions: ["pageshadow", "pagedim-black"],
        navbar: {
            title: searchSite
        }
    });

    $(document).on('click', '.toggle-menu', function(e){
        e.preventDefault();
        var menuApi = $('#my-menu').data('mmenu');
        menuApi.open();
    });

    $(document).on('click', '.toggle-menu2', function(e){
        e.preventDefault();
        var menuApi = $('#my-menu2').data('mmenu');
        menuApi.open();
    });


    //custom menu handling
    $('a[data-popover]').each(function(){

        var content = '<ul>' + $(this).next('ul').first().html() + '</ul>';

        $(this).webuiPopover({
            content: content,
            trigger: 'hover',
            animation: 'fade',
            // arrow: false,
            position: 'bottom',
            // offsetTop: 4,
            template: '<div class="webui-popover navigation-popover">' +
            '<div class="arrow"></div>' +
            '<div class="webui-popover-inner">' +
            '<a href="#" class="close">&times;</a>' +
            '<h3 class="webui-popover-title"></h3>' +
            '<div class="webui-popover-content"><i class="icon-refresh"></i> <p>&nbsp;</p></div>' +
            '</div>' +
            '</div>',
        });
    });
    //
    // $('.navlist-wrapper').scrollableNav({
    //     elem: '.navlist'
    // });

});
(function($){

    var active_tags = [];

    var currentFilters = {};

    var addFieldToUrl = function(varName, varValue, appendValue){

        if (undefined !== varName && undefined !== varValue){
            if (undefined !== currentFilters[varName]){
                if (typeof currentFilters[varName] == 'object' && currentFilters[varName].indexOf(varValue) == -1 && varName !== 'price' && appendValue == true ){
                    currentFilters[varName].push(varValue);
                } else {
                    if (currentFilters[varName] !== varValue){
                        if (varName !== 'price' && appendValue == true){
                            var currentValue = currentFilters[varName];
                            currentFilters[varName] = [];
                            currentFilters[varName].push(currentValue);
                            currentFilters[varName].push(varValue);
                        } else {
                            currentFilters[varName] = varValue;
                        }
                    }
                }

            } else {
                currentFilters[varName] = varValue;
            }
        }

        console.log(currentFilters);

    };

    var buildUrl = function(filter, varName, varValue, appendValue){

        if (appendValue !== false){
            appendValue = true;
        }

        addFieldToUrl(varName, varValue, appendValue);

        var url_base = window.location.origin + window.location.pathname;

        var url_components = [];

        for(var i in currentFilters){
            if (typeof currentFilters[i] == 'object'){  
                for(var j in currentFilters[i]){
                    url_components.push(i + '=' + encodeURIComponent(currentFilters[i][j]));
                }
            } else {
                url_components.push(i + '=' + encodeURIComponent(currentFilters[i]));
            }
        }

        var query_string = '?' + url_components.join('&');

        window.location.href = url_base + (query_string != '?' ? query_string : '');
    };

    var removeUrlParam = function(label, value){
        if (undefined !== currentFilters[label]){
            if (typeof currentFilters[label] == 'object') {             
                var index = currentFilters[label].indexOf(value);

                if (index !== -1){
                    currentFilters[label].splice(index, 1);
                    buildUrl();
                }
            } else {
                currentFilters[label] = null;
                buildUrl();
            }
        }
    };

    var parseQueryString = function(){

        var queryString = decodeURIComponent(window.location.search);

        if (queryString[0] == '?'){
            queryString = queryString.substr(1);
        }

        var pairs = queryString.split('&');

        for (var i in pairs){
            if (pairs[i]){
                if (pairs[i].indexOf('=')){
                    var sub_pair = pairs[i].split('=');

                    if (undefined !== sub_pair[0] && undefined !== sub_pair[1]){

                        if (undefined == currentFilters[sub_pair[0]]){
                            currentFilters[sub_pair[0]] = sub_pair[1];
                        } else {

                            if (typeof currentFilters[sub_pair[0]] == 'object'){

                                currentFilters[sub_pair[0]].push(sub_pair[1]);

                            } else {

                                var currentVal = currentFilters[sub_pair[0]];

                                currentFilters[sub_pair[0]] = [];

                                currentFilters[sub_pair[0]].push(currentVal);

                                currentFilters[sub_pair[0]].push(sub_pair[1]);

                            }

                        }

                    }
                }
            }

        }
    };

    parseQueryString();

    var closeOnOutClickEvent = function(){

        $(document).mouseup(function (e)
        {
            var container = $('.top-filters-wrapper').first();

            if (!container.is(e.target)
                && container.has(e.target).length === 0 // ... nor a descendant of the container
            )
            {
                container.find('.open').removeClass('open');
            }
        });

    };

    //global event handler for sorting change
    $(document).on('click', '.sorting-dropdown .dropdown-menu a', function(e){
        e.preventDefault();
        e.stopPropagation();

        //search for filter form sorting field
        var sortingField = $(this).attr('data-sort-field');

        //search for filter form sorting direction
        var sortingDirection = $(this).attr('data-sort-direction');

        addFieldToUrl('sort_by', sortingField, false);

        buildUrl(null, 'sort_dir', sortingDirection, false);

    });

    $.fn.siteFilters = function(options){

        closeOnOutClickEvent();

        $(this).find('.filter').each(function(){

            var $filter = $(this);
            var filterTagName = $filter.attr('data-tag-name');
            var selectedValues = null;
            var currentFilterValue = null;

            var attachEvents = function(){

                $filter.find('.filter-content').on('click', function(){

                    if ($filter.hasClass('remove-filters')){
                        currentFilters = {};
                        buildUrl();
                        return;
                    }

                    var $dropdown = $filter.find('.filter-dropdown').first();

                    if (!$dropdown.hasClass('open')){

                        $('.filter-dropdown.open').removeClass('open');

                        var params = {
                            tagName: filterTagName,
                            qs_params: currentFilters,
                            source: options.source,
                            source_id: options.source_id
                        };

                        if (!$dropdown.hasClass('filter-ready')){

                            $dropdown.find('.scrollbar-rail').scrollbar({
                                disableBodyScroll: true,
                                autoUpdate: true
                            });

                            $dropdown.find('.scroll-content').empty();

                            $dropdown.addClass('open loading');


                            $.post(window.app_prefix + '/get-tags', params)
                                .success(function(res){

                                    $dropdown.removeClass('loading');

                                    if (res.contentType == 'slider'){

                                        $dropdown.addClass('with-slider');
                                        // $dropdown.find('.filter-dropdown-content').empty();
                                        $dropdown.find('.filter-dropdown-content').append($(res.content));

                                        var $slider = $dropdown.find('#price-slider');

                                        var slider = noUiSlider.create(document.getElementById('price-slider'),{
                                            start: [ parseInt($slider.attr('data-min-price')) , parseInt($slider.attr('data-max-price'))],
                                            step: 1,
                                            range: {
                                                'min': [ parseInt($slider.attr('data-range-min')) ],
                                                'max': [ parseInt($slider.attr('data-range-max')) ]
                                            },
                                        });

                                        slider.on('change', function(range){
                                            range = range.map(function(v){
                                                return parseInt(v);
                                            });
                                            buildUrl($filter, 'price', range.join('-'))
                                        });

                                        slider.on('slide', function(e){
                                            $('#price-slider-sample').text(parseInt(e[0]) + ' - ' + parseInt(e[1]));
                                        });

                                    } else {
                                        $dropdown.find('.scroll-content').append($(res.content));
                                    }

                                    $dropdown.addClass('filter-ready');

                                })
                                .fail(function(){

                                });

                        } else {
                            $dropdown.addClass('open');
                        }

                    } else {
                        $dropdown.removeClass('open');
                    }

                });



                $filter.on('click', '.filter-tag-list li', function(){
                    buildUrl($filter, $(this).attr('data-var-name'), $(this).attr('data-var-value'));
                });

                $filter.on('click', '.used-filters-list li', function(){
                    removeUrlParam($(this).attr('data-filter-label'), $(this).attr('data-filter-id'));
                });

                $filter.on('keyup', '.filter-search-input', function(e){
                    if ($(this).val() && $(this).val() != currentFilterValue){
                        currentFilterValue = $(this).val().toLowerCase();

                        $filter.find('.filter-tag-list li').each(function(){
                            if ($(this).text().toLowerCase().indexOf(currentFilterValue) != -1){
                                $(this).removeClass('hidden');
                            } else {
                                $(this).addClass('hidden');
                            }
                        });
                        return;
                    }
                    if ($(this).val() == ''){
                        $filter.find('.filter-tag-list li').removeClass('hidden');
                    }
                });
            };

            var init = function(){
                attachEvents();
            };

            init();

        });
    };

})(jQuery);

$(function(){
    $('#categoryCollapse').on('show.bs.collapse', function () {
        $('#filterCollapse').collapse('hide');
    });

    $('#filterCollapse').on('show.bs.collapse', function () {
        $('#categoryCollapse').collapse('hide');
    });


});

(function($) {
  $.fn.productModal = function() {
    return $(this).each(
      function() {
        var elem = $(this);
        var $modal = null;
        var $similar_products_html = null;
        var isMobile = $('#mobile-selector').first().css('display') == 'none';
        var is_parent = elem.attr('data-is-parent');
        var parent_id = elem.attr('data-product-parent-id');
        
        if (is_parent == "1")  {
          location.href = elem.attr('data-product-url');
          return;
        }
        
        if (parent_id != '') {
          $( '#alternativeForm_' + elem.attr('data-product-id') ).submit();
          return;
        }
    
        var cloneSimilarProductTemplate = function(data) {
          var html = $similar_products_html;
          html = html.replace(/%sim_product_image_alt%/gi, data.image_alt);
          html = html.replace(/data-sim_product_href=""/gi, 'href="'+data.url_key+'"');
          if (data.previous_price !== null && data.previous_price && data.previous_price !== 'null') {
            html = html.replace(/%sim_product_previous_price%/, data.previous_price);
          } 
          else {
            html = html.replace(/%sim_product_previous_price%/, '');
          }
          html = html.replace(/%sim_product_price%/gi, data.price);
          html = html.replace(/%sim_product_discount%/gi, data.discount);
          html = html.replace(/%sim_product_image_url%/gi, data.thumbnail);
          html = html.replace(/%sim_product_id%/gi, data.id);
          html = $(html);
          return html;
        }
        
        var fetchData = function(isMobile) {
          var d = $.Deferred();
          $.get(elem.attr('data-product-url'), 
            {
              mob: isMobile
            }
          )
          .success(
            function(res) {
              if (res.status == 'success') {
                d.resolve(res);
              } 
              else {
                d.reject(res);
              }
            }
          )
          .fail(
            function() {
              d.reject(arguments);
            }   
          );
          return d.promise();
        }
  
        var attachEvents = function() {
          $modal.find('.close-btn').click(
            function() {
              if (isMobile) {
                $modal.remove();
                $('body').removeClass('mobile-product-modal-open').removeClass('product-modal-open');
              } 
              else {
                $modal.slideUp('fast', 
                  function() {
                    $modal.remove();
                    $('body').removeClass('mobile-product-modal-open').removeClass('product-modal-open');
                  }
                );
              }
            }
          );
          $(window).on('resize', 
            function() {
              $('#mobile-selector').first().css('display') == 'none';
            }
          );
          $(document).on('scroll', 
            function() {
              if ($('body').hasClass('mobile-product-modal-open')) {
                if ($(document).scrollTop() > 100) {
                  if (!$modal.hasClass('header-fixed')) {
                    $modal.addClass('header-fixed');
                  }
                } 
                else {
                  $modal.removeClass('header-fixed');
                }
              }
            }
          );
        }
  
        var updateTemplate = function(template, data) {
          for (var index in data) {
            var re = new RegExp('%' + index + '%', 'gi');
            template = template.replace(re, data[index]);
            var urlRe = new RegExp('data-' + index+'=""' , 'gi');
            var sp = index.split('_');
            var spL = sp.length;
            var attr = sp[spL-1];
            if (attr == "href" || attr == "src") {
              template = template.replace(urlRe, attr+'="'+data[index]+'"');
            }
          }
          return template;
        }

        var init = function() {
          $('.product-tile-wrapper.active').removeClass('active');
          elem.addClass('active');
          var $parent = elem.parent();

          if ($('.product-modal.open').size() > 0) {
            $('.product-modal.open').slideUp('fast', 
              function() {
                $(this).remove();
              }
            );
            $('body').removeClass('mobile-product-modal-open').removeClass('product-modal-open');
          }

          var width = elem.width();
          var position = elem.position();
          var index = Math.ceil(position.left / parseInt(width));
          var elemCount = Math.ceil($parent.width() / width) - 1;
          var lastElem = elem;
          for (var i = 0; i < elemCount - index; i++) {
            var next = lastElem.next('.product-tile-wrapper');
            if (next.size() > 0) {
              lastElem = next;
            }   
          }
          var data = {
            product_title: elem.attr('data-product-title'),
            product_href: elem.attr('data-product-url'),
            product_price: elem.attr('data-product-price'),
            product_previous_price: elem.attr('data-product-pprice'),
            product_id: elem.attr('data-product-id'),
            product_image_src: elem.attr('data-image-url'),
            product_image_alt: elem.attr('data-image-alt'),
            product_product_id: elem.attr('data-product-product-id'),
            product_csrf_token: elem.attr('data-csrf_token'),
            product_route_product_shop: elem.attr('data-route_product_shop'),
            product_purchase_here_label: elem.attr('data-purchase_here_label')
          }
          $modal_html = updateTemplate($('#product-modal').html(), data);
          $modal =  $($modal_html);
          if (elem.attr('data-on-wishlist') == 'true') {
            $modal.find('.wishlist').addClass('on-wishlist');
          } 
          else {
            $modal.find('.wishlist').removeClass('on-wishlist');
          }
          $similar_products_html = $modal.find('#similar-product-template').html();
          $modal.addClass('open');
          if (isMobile) {
            $('body').append($modal);
            $('body').addClass('mobile-product-modal-open');
            $modal.show();
            $('body').scrollTop(0);
          } 
          else {
            $modal.insertAfter(lastElem);
            $('body').addClass('product-modal-open');
            $modal.slideDown('fast', 
              function() {
                $modal.animatedScroll(
                  {
                    duration: 'normal',
                    easing: 'linear'
                  }
                );
              }
            );
          }
          fetchData(isMobile).then(
            function(res) {
              var data = res.data;
              $modal.find('.modalMainImg').replaceWith('<img src="' + data.image_url + '" alt="' + data.image_alt + '"/>');
              if (undefined !== res.similar_products) {
                $modal.find('.similar-products').show();
                for (var i in res.similar_products) {
                  var sim_obj = cloneSimilarProductTemplate(res.similar_products[i]);
                  $modal.find('.similar-products .products-wrapper').append(sim_obj);
                }
              }
            }, 
            function() {
            }
          );
          attachEvents();                    
          if (is_parent == "0") {
            $('#childContent').hide();              
            $('#buttonsContent').show();              
          }                                     
          if (is_parent == "1") {
            $('#buttonsContent').hide();              
            $('#childContent').show();
            $.post(
              window.app_prefix + '/get-children',
              { product_id: data.product_product_id }
            )
            .success(
              function (res) {
                var cl_html = cl_html + '<br clear="all"/>';
                cl_html = cl_html + '<table class="fontSize12">';
                cl_html = cl_html + '<tr class="border_bottom">';
                cl_html = cl_html + '<td class="childrenTH" nowrap>&nbsp;</td>';
                cl_html = cl_html + '<td class="childrenTH" nowrap>&nbsp;</td>';
                res.parent_filters.forEach(
                  function callback(currentValue, index, array) {
                    cl_html = cl_html + '<td class="childrenTH" nowrap>' + currentValue +'</td>'  
                  }
                );
                cl_html = cl_html + '<td class="childrenTH" nowrap>Price</td>';
                cl_html + '<td class="childrenTH" nowrap>In stock</td>';
                cl_html + '<td class="childrenTH" nowrap>&nbsp;</td>';
                cl_html + '</tr>';
                res.children_products.forEach(
                  function callback(currentValue, index, array) {
                    cl_html = cl_html  + '<tr>';                                                                  
                    cl_html = cl_html  + '<td class="childrenTD" nowrap>';
                    cl_html = cl_html + '<img src="https://cdn.pursury.com/unsafe/50x50/smart/' + currentValue.thumbnail + '" alt="' + currentValue.image_alt + '">'; 
                    cl_html = cl_html + '</td nowrap>';
                    cl_html = cl_html + '<td class="childrenTD" nowrap>' + currentValue.title + '</td>';

                    res.parent_filters.forEach(
                      function callback(currentParentFilter, indexPF, arrayPF) {
                        cl_html = cl_html + '<td class="childrenTD" nowrap>' + res.parent_filters_values[currentParentFilter + '_' + currentValue.id] + '</td>';
                      }
                    );
                    
                    cl_html = cl_html + '<td nowrap>';
                    if (currentValue.previous_price) {
                      cl_html = cl_html + '<span class="old-price">' + currentValue.previous_price + '</span>';
                    }
                    cl_html = cl_html + currentValue.price;
                    cl_html = cl_html + '</td>'; 

                    cl_html = cl_html + '<td class="childrenTD" nowrap>';
                    if (currentValue.stock) {
                      cl_html = cl_html + 'Yes';
                    }
                    else {
                      cl_html = cl_html + 'No';
                    }
                    cl_html = cl_html + '</td>';
                    cl_html = cl_html + '<td class="childrenTD" nowrap>';
                    cl_html = cl_html + '<form action="' + data.product_route_product_shop + '" method="post" target="_blank">';
                    cl_html = cl_html + '<input type="hidden" name="_token" value="' + data.product_csrf_token + '">';
                    cl_html = cl_html + '<input type="hidden" name="product_id" value="' + currentValue.id + '" />';
                    cl_html = cl_html + '<button class="btn-ultra2 btn-shop prim-btn">' + data.product_purchase_here_label + '</button>';
                    cl_html = cl_html + '</form>';                                    
                    cl_html = cl_html + '</td>';                                    
                    cl_html = cl_html + '</tr>';                                  
                  }
                );
                $('#childrenLoop').html(cl_html);
              }
            );
          }
        }
        init();
      }
    );
  }
})(jQuery);
//# sourceMappingURL=front_app.js.map