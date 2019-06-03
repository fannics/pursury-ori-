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