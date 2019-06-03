<div class="product-tile-wrapper"
     data-product-url="{{ prefixed_route($product->url_key) }}"
     data-product-title="{{ $product->title }}"
     data-product-price="{{ print_price($product->price) }}"
     data-product-pprice="{{ $product->previous_price ? print_price($product->previous_price, true) : '' }}"
     data-product-id="{{ $product->id}}"
     data-image-url="{{ resized_image($product->thumbnail) }}"
     data-on-wishlist="{{ $product->on_wishlist ? 'true' : 'false' }}"
     data-image-alt="{{ $product->image_alt }}"
     data-is-parent="{{ $product->is_parent }}"
     data-product-product-id="{{ $product->product_id}}"                     
     data-csrf_token="{{ csrf_token() }}"
     data-route_product_shop="{{ route('product_shop') }}"
     data-purchase_here_label="{{ trans('main.product.purchase_here') }}"
     data-product-parent-id="{{ $product->parent_id }}"
     data-product-parent-url="{{ get_route_product($product->parent_id) }}"
>
    <div class="product-tile" >
        <div class="product-discounts">
            @if ($product->previous_price and $product->previous_price != $product->price)
                <span class="discount">{{ round(100 - ( floatval($product->price) * 100 / floatval($product->previous_price))). '%' }}</span>
            @endif
        </div>
        <div class="product-image">              
            <div class="image-async-loader" data-src="{{ resized_image($product->thumbnail) }}" data-alt="{{ $product->image_alt }}"></div>
            <div class="overlay">
                @if (!isset($hide_buttons))
                    <a class="first wishlist {{ isset($product->on_wishlist) || isset($wishlist_is_the_source) ? 'on-wishlist def-btn' : 'prim-btn' }} {{ isset($wishlist_is_the_source) ? 'remove_on_toggled' : '' }}" href="#" data-url="{{ route('wishlist_item') }}" data-pi="{{ $product->id }}" class="favorite-link"><i class="fa fa-heart fa-2x"></i></a>
                    <form action="{{ route('product_shop') }}" method="post" target="_blank">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="product_id" value="{{ $product->id }}" />
                        <button class="second prim-btn" rel="nofollow" target="_blank" class="product-file-link"><span>{{ trans('main.product_tile.product_tile_follow') }}</span>  <i class="fa fa-chevron-right"></i></button>
                    </form>
                    <form id="alternativeForm_{{ $product->id }}" action="{{ get_route_product($product->parent_id) }}" method="POST">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <input type="hidden" name="product_id" value="{{ $product->id }}">
                    </form>
                @endif
            </div>
        </div>
        <div class="product-title">
            <a class="product-title-link" title="{{ $product->title }}" href="{{ prefixed_route($product->url_key) }}">
                {{ $product->title }}
            </a>
        </div>
        <div class="product-price">
            @if($product->previous_price)
                <span class="old-price">{!! print_price($product->previous_price, true) !!}</span>
            @endif
            <span>{!! print_price($product->price) !!}</span>
        </div>
    </div>
</div>