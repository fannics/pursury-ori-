@extends('master')

@section('metadata')
    <meta NAME="robots" CONTENT="index, follow">
    <meta name="description" content="{{ trans('main.index.meta_description') }}">

    <meta property="og:title" content="{{ settings('app.app_title').' - '.trans('main.index.most_popular') }}" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="{{ asset(settings('app.route_prefix').'/images/watermarksincom_resized.png') }}" />
    <meta property="og:description" content="{{ trans('main.index.meta_description') }}" />
    <meta property="og:site_name" content="{{ settings('app.app_title') }}" />

@endsection
                                                                                                   
@section('title', settings('app.app_title').' - '.trans('main.index.most_popular'))

@section('main_content')
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-10 col-md-push-2">
            <div class="row">
                <div class="col-sm-12 text-center category-page-header">
                    <div class="category-navigation">
                        <h2 class="category-page-title">
                          {{ trans('main.index.most_popular_products') }}
                        </h2>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="filters">
                        <form action="" id="filter-form">
                            <input type="hidden" name="sorting_field" id="sorting-field" value="{{ $sorting_field }}" />
                            <input type="hidden" name="sorting_direction" id="sorting-direction" value="{{ $sorting_direction }}">
                        </form>
                        <div class="total pull-left">
                            {{ $products->total().' '.trans('main.index.products')  }}
                        </div>
                        <div class="total pull-right">
                            <div class="dropdown sorting-dropdown"> 
                                <a class="sorting-toggle dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    {{ trans('main.index.sorted_by') }}
                                    @if ($sorting_field == 'popularity')
                                      {{ trans('main.index.popularity') }}
                                    @elseif ($sorting_field == 'title')
                                      {{ trans('main.index.name') }}
                                    @elseif ($sorting_field == 'price' && $sorting_direction == 'DESC')
                                      {{ trans('main.index.price_more_expensive') }}
                                    @elseif ($sorting_field == 'price' && $sorting_direction == 'ASC')
                                      {{ trans('main.index.price_less_expensive') }}
                                    @endif
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <li><a data-sort-field="popularity" data-sort-direction="DESC" href="#">{{ trans('main.index.popularity') }}</a></li>
                                    <li><a data-sort-field="price" data-sort-direction="ASC"  href="#">{{ trans('main.index.price_less_expensive') }}</a></li>
                                    <li><a data-sort-field="price" data-sort-direction="DESC" href="#">{{ trans('main.index.price_more_expensive') }}</a></li>
                                    <li><a data-sort-field="title" data-sort-direction="ASC"  href="#">{{ trans('main.index.name') }}</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-12">
                    <hr style="margin-top: 5px;" />
                </div>
            </div>
            <div class="products-list index-list">
                @forelse($products as $product)
                                          
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
                                            <button class="second prim-btn" rel="nofollow" target="_blank" class="product-file-link"><span>{{ trans('main.index.see_product') }}</span>  <i class="fa fa-chevron-right"></i></button>
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
                                    <span class="old-price">{{ number_format($product->previous_price, 2) }}</span>
                                @endif
                                <span>{{ number_format($product->price, 2) }} &euro;</span>
                            </div>
                        </div>
                  </div>
                    
                @empty
                    <p class="text-center">{{ trans('main.index.no_products_show') }}</p>
                @endforelse
            </div>
            <div class="row">
                <div class="col-sm-12 text-center">
                    {!! $products->render() !!}
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-2 sidebar col-md-pull-10">
            <div class="sidebar-navigation-wrapper">
                <h2 class="category-page-title">
                  {{ trans('main.index.categories') }}
                </h2>
                <div class="placeholder"></div>
                <hr>
                @if ($categories->count() > 0)
                <ul class="subcategories">
                    @foreach($categories as $cat)
                        @if ($cat->parent_id == null)
                            <li><a href="{{ prefixed_route($cat->url_key) }}">{{ strtoupper($cat->title) }}</a></li>
                        @endif
                    @endforeach
                </ul>
                @else
                    <p class="text-center">{{ trans('main.index.no_categories_show') }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('other_scripts')
    @include ('main/sitelinksSearchBoxSchema')
@endsection