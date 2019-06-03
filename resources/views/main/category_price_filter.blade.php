<div class="filter-wrapper">
    <div class="clear-filter">
        <a href="#" class="clear-filter-btn"><i class="fa fa-times"></i> {{ trans('main.category_price_filter.remove_filter') }}</a>
    </div>
    <div class="filter-values">
        <div class="slider-container">
            <div id="price-slider" data-min="{{ round($price_range->min_price) }}" data-max="{{ round($price_range->max_price) }}"></div>
        </div>
        <div class="clearfix"></div>
        <p class="text-center">
            <b class="min" id="price-slider-lower">90</b><b>&euro;</b> - <b class="max" id="price-slider-upper">170</b><b>&euro;</b>
        </p>
        <div class="clearfix"></div>
        <div class="text-center">
            <a class="price-filter-trigger" href="#">{{ trans('main.category_price_filter.filter') }}</a>
        </div>
    </div>
</div>
