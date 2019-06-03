<div class="product-tile-wrapper">
{{--<div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 product-tile-wrapper">--}}
    <div class="product-tile">
        <div class="product-discounts">
            @if ($product->previous_price and $product->previous_price != $product->price)
                <span class="discount">{{ round(100 - ( floatval($product->price) * 100 / floatval($product->previous_price))). '%' }}</span>
            @endif
        </div>
        <div class="product-image">
            <div class="image-async-loader" data-src="{{ resized_image($product->thumbnail) }}" data-alt="{{ $product->image_alt }}"></div>
            <div class="overlay">
                <div class="info">
                    <div>
                        {{ $product->hits ? $product->hits : 0 }} {{ trans('admin.product_tile.views') }}
                    </div>
                    <div>
                        {{ $product->shop_visits ? $product->shop_visits : 0  }} {{ trans('admin.product_tile.store_visits') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="product-title">
            <a href="{{ prefixed_route($product->url_key) }}">
                {{ $product->title }}
            </a>
        </div>
        <div class="product-price">
            @if($product->previous_price)
                <span class="old-price">{{ number_format($product->previous_price, 2) }}</span>
            @endif
            <span>{{ number_format($product->price, 2) }} &euro;</span>
        </div>

    </div>
{{--</div>--}}
</div>