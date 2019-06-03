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