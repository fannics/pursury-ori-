<div class="price-slider-wrapper">
    <div class="price-slider-min left">{{ round($price_range->min_price) }}</div>
    <div class="price-slider left"
         id="price-slider"
         data-min-price="{{ $price_filter[0] }}"
         data-max-price="{{ $price_filter[1] }}"
         data-range-max="{{ $price_range->max_price }}"
         data-range-min="{{ $price_range->min_price }}"
    ></div>
    <div class="price-slider-max left">{{ round($price_range->max_price) }}</div>
</div>
<div class="text-center">
    <b id="price-slider-sample">{{ $price_filter[0].' - '.$price_filter[1] }}</b>
</div>
