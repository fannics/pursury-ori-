<div class="product-tile-wrapper" data-product-url="{{ prefixed_route($product->url_key) }}">
{{--<div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 product-tile-wrapper">--}}
    <div class="product-tile wishlist-tile">
        <div class="product-discounts">
            @if ($product->previous_price and $product->previous_price != $product->price)
                <span class="discount">{{ round(100 - ( floatval($product->price) * 100 / floatval($product->previous_price))). '%' }}</span>
            @endif
        </div>
        <div class="product-image">
            <div class="image-async-loader" data-src="{{ resized_image($product->thumbnail) }}" data-alt="{{ $product->title }}"></div>
            <div class="overlay">
                <div class="info">
                    <div>
                        <a class="wishlist remove_on_toggled rp def-btn" href="#" data-url="{{ route('wishlist_item') }}" data-pi="{{ $product->id }}" >{{ trans('main.wishlist_tile.wishlist_remove') }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="product-title">
            <a class="product-title-link" title="{{ $product->title }}" href="{{ prefixed_route($product->url_key) }}">
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