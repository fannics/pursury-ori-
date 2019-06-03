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
$(function(){

    var productTitleMinLength = (typeof global_productTitleMinLength === 'undefined') ? 'Product title must have at least 4 characters' : global_productTitleMinLength;
    var productDestinationURLURL = (typeof global_productDestinationURLURL === 'undefined') ? 'You must enter a valid URL' : global_productDestinationURLURL;
    var productPriceNumber = (typeof global_productPriceNumber === 'undefined') ? 'You must enter a valid number' : global_productPriceNumber; 
    var productPriceMin = (typeof global_productPriceMin === 'undefined') ? 'You must enter a number greater than 0' : global_productPriceMin;
    var productPreviousPriceNumber = (typeof global_productPreviousPriceNumber === 'undefined') ? 'You must enter a valid number' : global_productPreviousPriceNumber;
    var productPreviousPriceMin = (typeof global_productPreviousPriceMin === 'undefined') ? 'You must enter a number greater than 0' : global_productPreviousPriceMin;
    var productCouponURLURL = (typeof global_productCouponURLURL === 'undefined') ? 'You must enter a valid URL' : global_productCouponURLURL; 

    $('#product-category').select2();
    
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
$(document).on('click', '#send-mail-btn', function(e){
    e.preventDefault();
    var alertContainer = $('#test-email-form .alert').first();
    alertContainer.attr('class', 'alert');
    alertContainer.empty();
    alertContainer.hide();
    $.post($('#test-email-form').attr('action'), {email: $('#email_address').val(), template: $('#email_template').val()})
        .success(function(res){
            if (res.status == 'not_valid'){

                alertContainer.addClass('alert-danger');

                for (var i in res.messages){
                    alertContainer.append('<p>' + res.messages[i] + '</p>')
                }

                alertContainer.show();

                return;
            }
            if (res.status == 'success'){
                alertContainer.addClass('alert-success');

                var testEmailSucccess = (typeof global_testEmailSucccess === 'undefined') ? 'The test e-mail has been sent successfully.' : global_testEmailSucccess;
                alertContainer.append('<p>' + testEmailSucccess + '</p>');

                alertContainer.show();
                return;
            }
        })
        .fail(function(){
            alert('error');
        });
});
$(function(){

    $(document).on('click', '.global-index-action', function(e){
        e.preventDefault();

        var that = this;

        bootbox.confirm($(that).attr('data-confirm-message'), function(res){
            if (res == true){

                var icon = $(that).find('i');                        

                icon.removeClass(icon.attr('data-regular-icon'));                                                              

                icon.addClass('fa-spinner fa-spin');
                
                $.post($(that).attr('href'))
                    .success(function(res){
                        bootbox.alert(res.message);
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        if ( (jqXHR.readyState == 0) && (jqXHR.status == 0) && (jqXHR.statusText == "error") ) {
                          bootbox.alert(global_actionhasFinished);
                          icon.removeClass('fa-spinner fa-spin');
                          icon.addClass(icon.attr('data-regular-icon'));
                        }
                        else {
                          var errorHappened = (typeof global_errorHappened === 'undefined') ? 'There was an error when trying to execute the required action' : global_errorHappened;
                          bootbox.alert(errorHappened);
                        }
                        icon.removeClass('fa-spinner fa-spin');
                        icon.addClass(icon.attr('data-regular-icon'));
                    })
                    .done(function(){
                        icon.removeClass('fa-spinner fa-spin');
                        icon.addClass(icon.attr('data-regular-icon'));
                    });
            }
        });

    });

});
$(function(){

    //var items = [];

    var tableRowTemplate = '<tr>' +
                                '<td>{title}</td>' +
                                '<td class="text-center"><input type="checkbox" class="change-child-visibility" {checked} /></td>' +
                                '<td class="text-center">' +
                                    '<div class="custom-slider">' +
                                        '<span class="value"></span>' +
                                        '<span class="controls">' +
                                            '<a href="#" class="change-order inc-control"><i class="fa fa-chevron-up"></i></a>' +
                                            '<a href="#" class="change-order dec-control"><i class="fa fa-chevron-down"></i></a>' +
                                        '</span>' +
                                    '</div>' +
                                '</td>' +
                                '<td>' +
                                    '<a class="remove-menu-item-button" href="#"><i class="fa fa-trash-o"></i></a></td>' +
                            '</tr>';

    var navbarDropdownTemplate = '<li class="dropdown">' +
                                    '<a href="{menu_link}" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{menu_title}<span class="caret"></span></a>' +
                                    '<ul class="dropdown-menu" role="menu">' +
                                        '{child}' +
                                    '</ul>' +
                                 '</li>';

    var navbarDropdownChildTemplate = '<li><a href="{children_link}">{children_title}</a></li>';

    var navbarSingleItemTemplate = '<li><a href="{menu_link}">{menu_title}</a></li>';


    var updateServer = function(){
        $.post($('.menu-definition').attr('data-action-url'), {action: 'update', items: window.menu_definition})
            .success(function(){

            })
            .error(function(){

            });
    };

    var generateTableAndPreview = function(){

        $('.menu-definition tbody tr').each(function(){
            if (!$(this).hasClass('no-items-row')){
                $(this).remove();
            }
        });

        $('.navbar-items').empty();

        if (window.menu_definition.length == 0){

            $('.menu-definition').addClass('no-items');

        } else {

            $.each(window.menu_definition, function(){

                var element = tableRowTemplate
                    .replace('{title}', this.title)
                    .replace('{checked}', this.display_children ? 'checked="checked"' : '');

                element = $(element);

                element.prop('bound-data', this);

                $('.menu-definition tbody').append(element);

                //update the preview
                var listItem = '';
                var children = '';

                if (this.display_children && this.children != undefined && this.children.length > 0){
                    listItem = navbarDropdownTemplate
                        .replace('{menu_title}', this.title)
                        .replace('{menu_link}', this.url);

                    $.each(this.children, function(){
                        children += navbarDropdownChildTemplate
                            .replace('{children_title}', this.title)
                            .replace('{children_link}', this.url);
                    });

                    listItem = listItem.replace('{child}', children);

                    $('.navbar-items').append($(listItem));

                } else {

                    listItem = navbarSingleItemTemplate
                        .replace('{menu_title}', this.title)
                        .replace('{menu_link}', this.url);

                    $('.navbar-items').append($(listItem));
                }

            });

        }

        $('input[type=checkbox], input[type=radio]').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '0'
        });
    };

    var sortItems = function(){

        window.menu_definition = window.menu_definition.sort(function(a, b){
            return a.position - b.position;
        });

    };

    var setInitialPositions = function(){
        var pos = 1;
        for (var i in window.menu_definition){
            window.menu_definition[i].position = pos;
            window.menu_definition[i].index = i;
            pos++;
        }
    };

    var moveItem = function(item, direction){

        switch(direction){
            case 'up':
                //find previous item
                window.menu_definition[parseInt(item.index) - 1].position = parseInt(window.menu_definition[parseInt(item.index) - 1].position) + 1;
                window.menu_definition[item.index].position = parseInt(window.menu_definition[item.index].position) - 1;
                break;
            case 'down':
                //find next item
                window.menu_definition[parseInt(item.index) + 1].position = parseInt(window.menu_definition[parseInt(item.index) + 1].position) - 1;
                window.menu_definition[item.index].position = parseInt(window.menu_definition[item.index].position) + 1 ;
                break;
        }

        sortItems();
        setInitialPositions();
        generateTableAndPreview();
        updateServer();
    };

    var addItem = function(category, display_children, index){
        $.post($('.menu-definition').attr('data-action-url'), {action: 'add', category: category, display_children: display_children, index: index })
            .success(function(res){
                if (res.status == 'success'){
                    //add the item at the end of menu definition and update the view
                    window.menu_definition.push(res.menu_item);
                    setInitialPositions();
                    generateTableAndPreview();
                    $('#new-item-modal').modal('hide');
                } else {
                    $('#add-item-form p.error').text(res.message);
                }
            })
            .error(function(){

            });
    };

    var init = function(){
        setInitialPositions();
        generateTableAndPreview();
    };

    if ($('.menu-configuration-page').size() > 0){
        init();
    }

    $(document).on('click', '.change-order', function(e){
        e.preventDefault();

        var $row = $(this).closest('tr');
        var data = $row.prop('bound-data');
        if ($(this).hasClass('inc-control')){
            if (data.index != 0){
                moveItem(data, 'up');
            }
        } else {
            if (data.index != (window.menu_definition.length - 1)){
                moveItem(data, 'down');
            }
        }
    });

    $(document).on('ifChanged', '.change-child-visibility', function(){

        var data = $(this).closest('tr').prop('bound-data');

        window.menu_definition[data.index].display_children = $(this).prop('checked');

        generateTableAndPreview();
    });

    $(document).on('click', '.add-item', function(e){
        e.preventDefault();

        $('#new-item-modal').modal({
            show: true,
            backdrop: 'static',
            keyboard: true
        });

    });

    $(document).on('click', '.add-item-submit', function(e){
        e.preventDefault();

        var category = $('#category').val();
        var display_children = $('#display-children').val();

        addItem(category, display_children, $('.menu-definition tbody tr').size() - 1);

    });

    $(document).on('click', '.remove-menu-item-button', function(e){
        e.preventDefault();

        var $row = $(this).closest('tr');
        var data = $row.prop('bound-data');

        $.post($('.menu-definition').attr('data-action-url'), {action: 'remove', 'id': data.id})
            .success(function(){
                $row.remove();

                window.menu_definition.splice(data.index, 1);

                setInitialPositions();
                sortItems();
                updateServer();
                generateTableAndPreview();

            })
            .fail(function(){
                alert('error');
            });

    });

});
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

$(function(){
    $('.logs-table-container').on('click', '.expand', function(){
        $('#' + $(this).attr('data-display')).toggle();
    });
});

(function(){

    $.fn.infiniteLoading = function(options, params){

        return $(this).each(function(){

            var that = this;

            var api = {
                setMessage: function(message){
                    $(that).closest('.modal-body').find('.message-wrapper').text(message);
                },
                setPersistentMessage: function(message){
                    $(that).closest('.modal-body').find('.persistent-messages').append(message);
                },
                setCompleted: function(){
                    $(that).prop('completed', true);
                    $(that).find('.progress-bar').addClass('progress-bar-success');
                    $(that).closest('.modal-body').find('.close-button-wrapper').show();
                },
                resetData: function(){
                    $(that).closest('.modal-body').find('.message-wrapper').empty();
                    $(that).closest('.modal-body').find('.persistent-messages').empty();
                    $(that).closest('.modal-body').find('.close-button-wrapper').hide();
                    $(that).find('.progress-bar').removeClass('progress-bar-success');
                }
            };

            var init = function(){
                $(that).addClass('il');

            };

            if (!$(that).hasClass('il')){

                init();
            } else {
                if (options !== undefined){
                    api[options](params);
                }
            }

        });
    };
});

$(
  function() {
  
    if ($('.product-import').size() > 0) {
      var file = null;
      
      var setMessage = function(elem, message) {
        $(elem).closest('.modal-body').find('.message-wrapper').html(message);
      };

      var setPersistentMessage = function(elem, message) {
        $(elem).closest('.modal-body').find('.persistent-messages').append(message);
      };

      var setCompleted = function(elem, message) {
        $(elem).prop('completed', true);
        $(elem).find('.progress-bar').addClass('progress-bar-success');
        $(elem).closest('.modal-body').find('.close-button-wrapper').show();
        setMessage(elem, message);
      };
                                                                     
      var resetData = function(elem) {
        $(elem).closest('.modal-body').find('.message-wrapper').empty();
        $(elem).closest('.modal-body').find('.persistent-messages').empty();
        $(elem).closest('.modal-body').find('.close-button-wrapper').hide();
        $(elem).find('.progress-bar').removeClass('progress-bar-success');
      };

      $('#loading-modal').on('shown.bs.modal', 
        function (e) {
          var elem = $('.infinite-progress');
          var import_id = null;
          resetData(elem);
          var importingProducts = (typeof global_importingProducts === 'undefined') ? 'Importing products' : global_importingProducts;
          setMessage(elem, global_importingProducts);

          $.post(
            window.app_prefix + '/admin/products/import'  + '/'+ $('#fileupload').attr('importtype'), {
             file: file
            }
          )
          .success(
  
            function(res) {
  
              if (res.status == 'success') {
  
                $('.import-results').html(res.results);
                import_id = res.import_id;
                setPersistentMessage(elem, '<p class="success">' + res.message + '</p>');
                var updatingAppRoutes = (typeof global_updatingAppRoutes === 'undefined') ? 'Updating application routes' : global_updatingAppRoutes;
                setMessage(elem, updatingAppRoutes);
                $.post(window.app_prefix + '/admin/products/update_routes', {import_id: import_id})
                .success(
  
                  function(res) {
  
                    if (res.status == 'success') {
                      var updatingSearchEngine = (typeof global_updatingSearchEngine === 'undefined') ? 'Updating search engine' : global_updatingSearchEngine;
                      setMessage(elem, updatingSearchEngine);
                      setPersistentMessage(elem, '<p class="success">' + res.message + '</p>');
                      $.post(window.app_prefix + '/admin/search-engine-refresh', {import_id: import_id})
                      .success(
                    
                        function(res) {
                    
                          if (res.status == 'success') {
                            setPersistentMessage(elem, '<p class="success">' + res.message + '</p>');
                            var actionhasFinished = (typeof global_actionhasFinished === 'undefined') ? 'Action has finished' : global_actionhasFinished;
                            setCompleted(elem, actionhasFinished);
                          } 
                    
                          else {
                            setPersistentMessage(elem, '<p class="error">' + res.message + '</p>');
                          }
                    
                        }
                      
                      )
                      .fail(
                        
                        function() {
                          var errorUpdatingIndex = (typeof global_errorUpdatingIndex === 'undefined') ? 'An error has happened when trying to update the search index' : global_errorUpdatingIndex;
                          setPersistentMessage(elem, '<p class="error">' + errorUpdatingIndex + '</p>');
                        }
                      );
                    } 
                  
                    else {
                      setPersistentMessage(elem, '<p class="error">' + res.message + '</p>');
                    }

                  }
                )
                .fail(
              
                  function() {
                    var errorUpdatingRoutes = (typeof global_errorUpdatingRoutes === 'undefined') ? 'An error has happened when trying to update the application routes' : global_errorUpdatingRoutes;
                    setPersistentMessage(elem, '<p class="error">' + errorUpdatingRoutes + '</p>');
                  }

                );
              } 
            
              else {
                setPersistentMessage(elem, '<p class="error">' + res.message + '</p>');
              }

            }
          
          )
          .fail(                                 
        
            function (res, textStatus, errorThrown) {
              if ( (res.readyState == 0) && (res.status == 0) && (res.statusText == "error") ) {
                $('.import-results').html(res.results);
                import_id = res.import_id;
                setPersistentMessage(elem, '<p class="success"></p>');
                var updatingAppRoutes = (typeof global_updatingAppRoutes === 'undefined') ? 'Updating application routes' : global_updatingAppRoutes;
                setMessage(elem, updatingAppRoutes);
                $.post(window.app_prefix + '/admin/products/update_routes', {import_id: import_id})
                .success(
  
                  function(res) {
  
                    if (res.status == 'success') {
                      var updatingSearchEngine = (typeof global_updatingSearchEngine === 'undefined') ? 'Updating search engine' : global_updatingSearchEngine;
                      setMessage(elem, updatingSearchEngine);
                      setPersistentMessage(elem, '<p class="success">' + res.message + '</p>');
                      $.post(window.app_prefix + '/admin/search-engine-refresh', {import_id: import_id})
                      .success(
                    
                        function(res) {
                    
                          if (res.status == 'success') {
                            setPersistentMessage(elem, '<p class="success">' + res.message + '</p>');
                            var actionhasFinished = (typeof global_actionhasFinished === 'undefined') ? 'Action has finished' : global_actionhasFinished;
                            setCompleted(elem, actionhasFinished);
                          } 
                    
                          else {
                            setPersistentMessage(elem, '<p class="error">' + res.message + '</p>');
                          }
                    
                        }
                      
                      )
                      .fail(
                        
                        function() {
                          var errorUpdatingIndex = (typeof global_errorUpdatingIndex === 'undefined') ? 'An error has happened when trying to update the search index' : global_errorUpdatingIndex;
                          setPersistentMessage(elem, '<p class="error">' + errorUpdatingIndex + '</p>');
                        }
                      );
                    } 
                  
                    else {
                      setPersistentMessage(elem, '<p class="error">' + res.message + '</p>');
                    }

                  }
                )
                .fail(
              
                  function(res, textStatus, errorThrown) {
                    if ( (res.readyState == 0) && (res.status == 0) && (res.statusText == "error") ) {
                      var updatingSearchEngine = (typeof global_updatingSearchEngine === 'undefined') ? 'Updating search engine' : global_updatingSearchEngine;
                      setMessage(elem, updatingSearchEngine);
                      setPersistentMessage(elem, '<p class="success"></p>');
                      $.post(window.app_prefix + '/admin/search-engine-refresh', {import_id: import_id})
                      .success(
                    
                        function(res) {
                    
                          if (res.status == 'success') {
                            setPersistentMessage(elem, '<p class="success">' + res.message + '</p>');
                            var actionhasFinished = (typeof global_actionhasFinished === 'undefined') ? 'Action has finished' : global_actionhasFinished;
                            setCompleted(elem, actionhasFinished);
                          } 
                    
                          else {
                            setPersistentMessage(elem, '<p class="error">' + res.message + '</p>');
                          }
                    
                        }
                      
                      )
                      .fail(
                        
                        function() {
                          var errorUpdatingIndex = (typeof global_errorUpdatingIndex === 'undefined') ? 'An error has happened when trying to update the search index' : global_errorUpdatingIndex;
                          setPersistentMessage(elem, '<p class="error">' + errorUpdatingIndex + '</p>');
                        }
                      );
                    }
                    else { 
                      var errorUpdatingRoutes = (typeof global_errorUpdatingRoutes === 'undefined') ? 'An error has happened when trying to update the application routes' : global_errorUpdatingRoutes;
                      setPersistentMessage(elem, '<p class="error">' + errorUpdatingRoutes + '</p>');
                    }
                  }

                );

              }
              else {
                var errorImportingFile = (typeof global_errorImportingFile === 'undefined') ? 'An error has happened when trying to import the file. Try again.' : global_errorImportingFile;
                setPersistentMessage(elem, '<p class="error">' + errorImportingFile + '</p>');
              }
          
            }
          );
        }
      );

      $('#loading-modal').modal(
        {
          backdrop: 'static',
          keyboard: false,
          show: false
        }
      );

      $('#fileupload').fileupload(
        {
          url: window.app_prefix + '/admin/products/upload',
          dataType: 'json',
          send: function(e, data) {
                  var elem = $('#upload-btn > span');
                  var text = elem.text();
                  elem.text(elem.attr('data-loading-text'));
                  elem.attr('data-loading-text', text);
                  $('.import-results').empty();
                },
          done: function (e, data) {
                  var result = data.result;
                  if (result.status == 'success') {
                    file = result.filename;
                    $('#loading-modal').modal('show');
                  }
                  else {

                    var errorUploadingFile = (typeof global_errorUploadingFile === 'undefined') ? global_errorUploadingFile : result.message;

                      bootbox.alert(errorUploadingFile);
                  }
                },
            always: function(e, data) {
                      var elem = $('#upload-btn > span');
                      var text = elem.text();
                      elem.text(elem.attr('data-loading-text'));
                      elem.attr('data-loading-text', text);
                    }
        }
      )
      .prop('disabled', !$.support.fileInput)
      .parent().addClass($.support.fileInput ? undefined : 'disabled');
    }

  }
);

$(function(){

    if ($('.category-import').size() > 0){
        var file = null;

        var setMessage = function(elem, message){
            $(elem).closest('.modal-body').find('.message-wrapper').html(message);
        };

        var setPersistentMessage = function(elem, message){
            $(elem).closest('.modal-body').find('.persistent-messages').append(message);
        };

        var setCompleted = function(elem, message){
            $(elem).prop('completed', true);
            $(elem).find('.progress-bar').addClass('progress-bar-success');
            $(elem).closest('.modal-body').find('.close-button-wrapper').show();
            setMessage(elem, message);
        };

        var resetData = function(elem){
            $(elem).closest('.modal-body').find('.message-wrapper').empty();
            $(elem).closest('.modal-body').find('.persistent-messages').empty();
            $(elem).closest('.modal-body').find('.close-button-wrapper').hide();
            $(elem).find('.progress-bar').removeClass('progress-bar-success');
        };

        $('#loading-modal').on('shown.bs.modal', function (e) {

            var elem = $('.infinite-progress');

            resetData(elem);

            var importingCategories = (typeof global_importingCategories === 'undefined') ? 'Importing categories' : global_importingCategories;
            setMessage(elem, importingCategories);

            $.post(window.app_prefix + '/admin/categories/import', {
                    file: file
                })
                .success(function(res){

                    if (res.status == 'success'){
                        country =  res.country;
                        language= res.language;

                        $('.import-results').html(res.results);

                        setPersistentMessage(elem, '<p class="success">' + res.message + '</p>');
            
                        var updatingAppRoutes = (typeof global_updatingAppRoutes === 'undefined') ? 'Updating application routes' : global_updatingAppRoutes;
                        setMessage(elem, updatingAppRoutes);

                        $.post(window.app_prefix + '/admin/products/update_categories_routes',{country: country,language:language})
                            .success(function(res) {
                                if (res.status == 'success'){
                                    var updatingCategoryTree = (typeof global_updatingCategoryTree === 'undefined') ? 'Updating categories tree' : global_updatingCategoryTree;
                                    setMessage(elem, updatingCategoryTree);
                                    setPersistentMessage(elem, '<p class="success">' + res.message + '</p>');

                                    $.post(window.app_prefix + '/admin/products/update_category_tree',{country: country,language:language})
                                        .success(function(res) {
                                            if (res.status == 'success'){
                                                setPersistentMessage(elem, '<p class="success">' + res.message + '</p>');
                                                var actionhasFinished = (typeof global_actionhasFinished === 'undefined') ? 'Action has finished' : global_actionhasFinished;
                                                setCompleted(elem, actionhasFinished);
                                            } else {
                                                setPersistentMessage(elem, '<p class="error">' + res.message + '</p>');
                                                var actionNotDone = (typeof global_actionNotDone === 'undefined') ? 'Action has not been done' : global_actionNotDone;
                                                setCompleted(elem, actionNotDone);
                                            }
                                        })
                                        .fail(function() {
                                            var errorUpdatingCategoriesTree = (typeof global_errorUpdatingCategoriesTree === 'undefined') ? 'There was an error when trying to update the categories tree' : global_errorUpdatingCategoriesTree;
                                            setPersistentMessage(elem, '<p class="error">' + errorUpdatingCategoriesTree + '</p>');
                                            var actionNotDone = (typeof global_actionNotDone === 'undefined') ? 'Action has not been done' : global_actionNotDone;
                                            setCompleted(elem, actionNotDone);
                                        });

                                } 
                                else {
                                    setPersistentMessage(elem, '<p class="error">' + res.message + '</p>');
                                    var actionNotDone = (typeof global_actionNotDone === 'undefined') ? 'Action has not been done' : global_actionNotDone;
                                    setCompleted(elem, actionNotDone);

                                }

                            })
                            .fail(function() {
                                var errorUpdatingRoutes = (typeof global_errorUpdatingRoutes === 'undefined') ? 'An error has happened when trying to update the application routes' : global_errorUpdatingRoutes;
                                setPersistentMessage(elem, '<p class="error">' + errorUpdatingRoutes + '</p>');
                                var actionNotDone = (typeof global_actionNotDone === 'undefined') ? 'Action has not been done' : global_actionNotDone;
                                setCompleted(elem, actionNotDone);

                            });

                    } else {

                        setPersistentMessage(elem, '<p class="error">' + res.message + '</p>');
                        var actionNotDone = (typeof global_actionNotDone === 'undefined') ? 'Action has not been done' : global_actionNotDone;
                        setCompleted(elem, actionNotDone);

                    }

                })
                .fail(function(){
                    var errorImportingFile = (typeof global_errorImportingFile === 'undefined') ? 'An error has happened when trying to import the file. Try again.' : global_errorImportingFile;
                    setPersistentMessage(elem, '<p class="error">' + errorImportingFile + '</p>');
                    var actionNotDone = (typeof global_actionNotDone === 'undefined') ? 'Action has not been done' : global_actionNotDone;
                    setCompleted(elem, actionNotDone);

                });
        });

        $('#loading-modal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });


        $('#fileupload').fileupload({
            url: window.app_prefix + '/admin/categories/upload',
            dataType: 'json',
            send: function(e, data){
                var elem = $('#upload-btn > span');
                var text = elem.text();
                elem.text(elem.attr('data-loading-text'));
                elem.attr('data-loading-text', text);
                $('.import-results').empty();
            },
            done: function (e, data) {
                var result = data.result;
                if (result.status == 'success'){
                    file = result.filename;
                    $('#loading-modal').modal('show');
                } else {
                    var errorUploadingFile = (typeof global_errorUploadingFile === 'undefined') ? global_errorUploadingFile : result.message ;
                    bootbox.alert(errorUploadingFile);
                }
            },
            always: function(e, data){
                var elem = $('#upload-btn > span');
                var text = elem.text();
                elem.text(elem.attr('data-loading-text'));
                elem.attr('data-loading-text', text);
            }
        }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');

    }
});

$(function(){

    if ($('.brands-import').size() > 0){
        var file = null;

        var setMessage = function(elem, message){
            $(elem).closest('.modal-body').find('.message-wrapper').html(message);
        };

        var setPersistentMessage = function(elem, message){
            $(elem).closest('.modal-body').find('.persistent-messages').append(message);
        };

        var setCompleted = function(elem, message){
            $(elem).prop('completed', true);
            $(elem).find('.progress-bar').addClass('progress-bar-success');
            $(elem).closest('.modal-body').find('.close-button-wrapper').show();
            setMessage(elem, message);
        };

        var resetData = function(elem){
            $(elem).closest('.modal-body').find('.message-wrapper').empty();
            $(elem).closest('.modal-body').find('.persistent-messages').empty();
            $(elem).closest('.modal-body').find('.close-button-wrapper').hide();
            $(elem).find('.progress-bar').removeClass('progress-bar-success');
        };

        $('#loading-modal').on('shown.bs.modal', function (e) {

            var elem = $('.infinite-progress');

            resetData(elem);

            var importingCategories = (typeof global_importingCategories === 'undefined') ? 'Importing brands' : global_importingCategories;
            setMessage(elem, 'Importing brands' );

            $.post(window.app_prefix + '/admin/brands/import', {
                file: file
            })
                .success(function(res){

                    if (res.status == 'success'){

                        $('.import-results').html(res.results);

                        setPersistentMessage(elem, '<p class="success">' + res.message + '</p>');
                        var actionhasFinished = (typeof global_actionhasFinished === 'undefined') ? 'Action has finished' : global_actionhasFinished;
                        setCompleted(elem, actionhasFinished);

                    } else {

                        setPersistentMessage(elem, '<p class="error">' + res.message + '</p>');
                        var actionNotDone = (typeof global_actionNotDone === 'undefined') ? 'Action has not been done' : global_actionNotDone;
                        setCompleted(elem, actionNotDone);

                    }

                })
                .fail(function(){
                    var errorImportingFile = (typeof global_errorImportingFile === 'undefined') ? 'An error has happened when trying to import the file. Try again.' : global_errorImportingFile;
                    setPersistentMessage(elem, '<p class="error">' + errorImportingFile + '</p>');
                    var actionNotDone = (typeof global_actionNotDone === 'undefined') ? 'Action has not been done' : global_actionNotDone;
                    setCompleted(elem, actionNotDone);

                });
        });

        $('#loading-modal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });


        $('#fileupload').fileupload({
            url: window.app_prefix + '/admin/brands/upload',
            dataType: 'json',
            send: function(e, data){
                var elem = $('#upload-btn > span');
                var text = elem.text();
                elem.text(elem.attr('data-loading-text'));
                elem.attr('data-loading-text', text);
                $('.import-results').empty();
            },
            done: function (e, data) {
                var result = data.result;
                if (result.status == 'success'){
                    file = result.filename;
                    $('#loading-modal').modal('show');
                } else {
                    var errorUploadingFile = (typeof global_errorUploadingFile === 'undefined') ? global_errorUploadingFile : result.message ;
                    bootbox.alert(errorUploadingFile);
                }
            },
            always: function(e, data){
                var elem = $('#upload-btn > span');
                var text = elem.text();
                elem.text(elem.attr('data-loading-text'));
                elem.attr('data-loading-text', text);
            }
        }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');

    }
});


$(function(){

    if ($('.stores-import').size() > 0){
        var file = null;

        var setMessage = function(elem, message){
            $(elem).closest('.modal-body').find('.message-wrapper').html(message);
        };

        var setPersistentMessage = function(elem, message){
            $(elem).closest('.modal-body').find('.persistent-messages').append(message);
        };

        var setCompleted = function(elem, message){
            $(elem).prop('completed', true);
            $(elem).find('.progress-bar').addClass('progress-bar-success');
            $(elem).closest('.modal-body').find('.close-button-wrapper').show();
            setMessage(elem, message);
        };

        var resetData = function(elem){
            $(elem).closest('.modal-body').find('.message-wrapper').empty();
            $(elem).closest('.modal-body').find('.persistent-messages').empty();
            $(elem).closest('.modal-body').find('.close-button-wrapper').hide();
            $(elem).find('.progress-bar').removeClass('progress-bar-success');
        };

        $('#loading-modal').on('shown.bs.modal', function (e) {

            var elem = $('.infinite-progress');

            resetData(elem);

            var importingCategories = (typeof global_importingCategories === 'undefined') ? 'Importing brands' : global_importingCategories;
            setMessage(elem, 'Importing stores' );

            $.post(window.app_prefix + '/admin/stores/import', {
                file: file
            })
                .success(function(res){

                    if (res.status == 'success'){

                        $('.import-results').html(res.results);

                        setPersistentMessage(elem, '<p class="success">' + res.message + '</p>');
                        var actionhasFinished = (typeof global_actionhasFinished === 'undefined') ? 'Action has finished' : global_actionhasFinished;
                        setCompleted(elem, actionhasFinished);

                    } else {

                        setPersistentMessage(elem, '<p class="error">' + res.message + '</p>');
                        var actionNotDone = (typeof global_actionNotDone === 'undefined') ? 'Action has not been done' : global_actionNotDone;
                        setCompleted(elem, actionNotDone);

                    }

                })
                .fail(function(){
                    var errorImportingFile = (typeof global_errorImportingFile === 'undefined') ? 'An error has happened when trying to import the file. Try again.' : global_errorImportingFile;
                    setPersistentMessage(elem, '<p class="error">' + errorImportingFile + '</p>');
                    var actionNotDone = (typeof global_actionNotDone === 'undefined') ? 'Action has not been done' : global_actionNotDone;
                    setCompleted(elem, actionNotDone);

                });
        });

        $('#loading-modal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });


        $('#fileupload').fileupload({
            url: window.app_prefix + '/admin/stores/upload',
            dataType: 'json',
            send: function(e, data){
                var elem = $('#upload-btn > span');
                var text = elem.text();
                elem.text(elem.attr('data-loading-text'));
                elem.attr('data-loading-text', text);
                $('.import-results').empty();
            },
            done: function (e, data) {
                var result = data.result;
                if (result.status == 'success'){
                    file = result.filename;
                    $('#loading-modal').modal('show');
                } else {
                    var errorUploadingFile = (typeof global_errorUploadingFile === 'undefined') ? global_errorUploadingFile : result.message ;
                    bootbox.alert(errorUploadingFile);
                }
            },
            always: function(e, data){
                var elem = $('#upload-btn > span');
                var text = elem.text();
                elem.text(elem.attr('data-loading-text'));
                elem.attr('data-loading-text', text);
            }
        }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');

    }
});

$(function() {
  if ($('.translation-import').size() > 0) {
    var file = null;
    var setMessage = function(elem, message) {
      $(elem).closest('.modal-body').find('.message-wrapper').html(message);
    };
    var setPersistentMessage = function(elem, message) {
      $(elem).closest('.modal-body').find('.persistent-messages').append(message);
    };
    var setCompleted = function(elem, message) {
      $(elem).prop('completed', true);
      $(elem).find('.progress-bar').addClass('progress-bar-success');
      $(elem).closest('.modal-body').find('.close-button-wrapper').show();
      setMessage(elem, message);
    };
    var resetData = function(elem) {
      $(elem).closest('.modal-body').find('.message-wrapper').empty();
      $(elem).closest('.modal-body').find('.persistent-messages').empty();
      $(elem).closest('.modal-body').find('.close-button-wrapper').hide();
      $(elem).find('.progress-bar').removeClass('progress-bar-success');
    };
    $('#loading-modal').on('shown.bs.modal', function (e) {
      var elem = $('.infinite-progress');
      resetData(elem);
      var importingTranslations = (typeof global_importingTranslations === 'undefined') ? 'Importing translations' : global_importingTranslations;
      setMessage(elem, importingTranslations);
      $.post(window.app_prefix + '/admin/translations/import', {
        file: file
      })
      .success(function(res) {
        if (res.status == 'success') {
          $('.import-results').html(res.results);
          var importHasFinised = (typeof global_importHasFinised === 'undefined') ? 'The import has finished successfully' : global_importHasFinised;
          setMessage(elem, importHasFinised);
          $('#loading-modal').modal('hide');
          $('#upload-button').hide();      
        } 
        else {
          setPersistentMessage(elem, '<p class="error">' + res.message + '</p>');
          var actionNotDone = (typeof global_actionNotDone === 'undefined') ? 'Action has not been done' : global_actionNotDone;
          setCompleted(elem, actionNotDone);
        }

      })
      .fail(function(res) {
        var errorImportingFile = (typeof global_errorImportingFile === 'undefined') ? 'An error has happened when trying to import the file. Try again.' : global_errorImportingFile;
        setPersistentMessage(elem, '<p class="error">' + errorImportingFile + '</p>' + JSON.stringify(res));
        var actionNotDone = (typeof global_actionNotDone === 'undefined') ? 'Action has not been done' : global_actionNotDone;
        setCompleted(elem, actionNotDone);
      });
    });
    $('#loading-modal').modal({
      backdrop: 'static',
      keyboard: false,
      show: false
    });
    $('#fileupload').fileupload({
      url: window.app_prefix + '/admin/translations/upload',
      dataType: 'json',
      send: function(e, data) {
        var elem = $('#upload-btn > span');
        var text = elem.text();
        elem.text(elem.attr('data-loading-text'));
        elem.attr('data-loading-text', text);
        $('.import-results').empty();
      },
      done: function (e, data) {
        var result = data.result;
        if (result.status == 'success') {
          file = result.filename;
          $('#loading-modal').modal('show');
        } 
        else {
          var errorUploadingFile = (typeof global_errorUploadingFile === 'undefined') ? 'An error has happened when trying to upload the file. Try again.' : global_errorUploadingFile;
          bootbox.alert(errorUploadingFile);
        }
      },
      always: function(e, data) {
        var elem = $('#upload-btn > span');
        var text = elem.text();
        elem.text(elem.attr('data-loading-text'));
        elem.attr('data-loading-text', text);
      }
    }).prop('disabled', !$.support.fileInput)
    .parent().addClass($.support.fileInput ? undefined : 'disabled');
  }
});



$(function(){

    $(document).on('click', 'a.global-action', function(e){

        e.preventDefault();
        e.stopPropagation();

        var $link = $(this);

        var postData = {
            type: $link.attr('data-otype'),
            id: $link.attr('data-oid'),
            action: $link.attr('data-action')
        };

        var performAction = function(){
            $.post(window.app_prefix + '/admin/global-action', postData)
                .success(function(res){

                    if ($link.closest('.dataTable').size() > 0){
                        $link.closest('.dataTable').dataTable().api().ajax.reload(function(){
                            bootbox.alert(res.message);
                        }, false);
                    }

                })
                .fail(function(res){
                    var errowWhenDoingAction = (typeof global_errowWhenDoingAction === 'undefined') ? 'An error has happened when trying to do this action.' : global_errowWhenDoingAction;
                    bootbox.alert(errowWhenDoingAction);
                });
        };


        if ($link.attr('data-confirm')){
            bootbox.confirm($link.attr('data-confirm'), function(res){
                if (res == true){
                    performAction();
                }
            });
        } else {
            performAction();
        }
    });

});
//# sourceMappingURL=admin_app.js.map
