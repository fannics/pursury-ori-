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