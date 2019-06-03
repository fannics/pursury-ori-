(function(){

    $.fn.scrollableNav = function(options){

        return $(this).each(function(){

            var that = this;

            var navList = $(that).find(options.elem);

            var viewportSize = $(that).width();

            var navListSize = $(navList).prop('scrollWidth');

            var changeNavigationVisibility = function(visible){
                if (visible){
                    $(that).addClass('with-navigation');
                } else {
                    $(that).removeClass('with-navigation');
                }
            };

            var canMoveAmount = function(direction){

                var position = $(navList).position();

                switch(direction){
                    case 'right':

                        var slideCount = Math.ceil(navListSize / viewportSize);

                        var currentSlide = Math.abs(Math.ceil(position.left / viewportSize)) + 1;

                        // console.log('slide count: ' + slideCount);
                        // console.log('current slide: ' + currentSlide);

                        if (currentSlide < slideCount){
                            return viewportSize;
                        } else {
                            return 0;
                        }

                    case 'left':

                        // console.log('position left: ' + position.left);
                        // console.log('viewportSize: ' + viewportSize);

                        if (position.left < 0){

                            var absLeft = Math.abs(position.left);

                            if (absLeft >= viewportSize){
                                return viewportSize;
                            } else {
                                return absLeft;
                            }

                        } else {
                            return 0;
                        }

                        break;
                }



            };

            var attachEvents = function(){

                $(window).on('resize', function(){

                    if ($(navList).prop('scrollWidth') > $(that).width()){
                        changeNavigationVisibility(true);
                    } else {
                        changeNavigationVisibility(false);
                    }

                    viewportSize = $(that).width();

                });

                $(that).on('click', '.navigation-left', function(e){

                    var moveAmount = canMoveAmount('left');

                    // console.log(moveAmount);

                    if (moveAmount != 0){
                        $(navList).animate({left: '+=' + moveAmount + 'px'});
                    } else {
                        // console.log('cannot move left');
                    }

                });

                $(that).on('click', '.navigation-right', function(e){

                    var moveAmount = canMoveAmount('right');

                    // console.log(moveAmount);

                    if (moveAmount != 0){
                        $(navList).animate({left: '-=' + moveAmount + 'px'});
                    } else {
                        // console.log('cannot move right');
                    }

                });
            };

            var init = function(){
                $(that).append('<div class="navigation-link navigation-left"><i class="fa fa-chevron-left"></i></div>');
                $(that).append('<div class="navigation-link navigation-right"><i class="fa fa-chevron-right"></i></div>')
                attachEvents();
                $(window).trigger('resize');
            };

            init();

        });

    };

})(jQuery);