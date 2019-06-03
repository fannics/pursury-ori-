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
            if (typeof currentFilters[label] == 'object'){
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