var noResults = (typeof global_noResults === 'undefined') ? "We're sorry, we cannot to find results for the terms" : global_noResults;
var showMoreResults = (typeof global_showMoreResults === 'undefined') ? 'Show more results' : global_showMoreResults;

$('#offcanvas-search-widget').searchWidget({
    url: function(term){
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
    moreResultsMessage: '<div class="results-footer text-center"><a href="more_url">' + showMoreResults + '</a></div>',
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