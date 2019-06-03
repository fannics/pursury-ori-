@extends('master')

@section('metadata')

    @if (!$category->meta_no_index)
        <meta NAME="robots" CONTENT="index, follow">
    @else
        <meta NAME="robots" CONTENT="noindex, nofollow">
    @endif
    <meta name="description" content="{{ $category->meta_description }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ settings('app.app_title').' - '.$category->title }}" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="{{ asset(settings('app.route_prefix').'/images/watermarksincom_resized.png') }}" />
    <meta property="og:description" content="{{ $category->short_description }}" />
    <meta property="og:site_name" content="{{ settings('app.app_title') }}" />

    @if ($products->nextPageUrl() > 1 && $products->previousPageUrl())
        <link rel="prev" href="{{ $products->previousPageUrl() }}">
        <link rel="next" href="{{ $products->nextPageUrl() }}">
    @else

        @if($products->previousPageUrl())
            <link rel="prev" href="{{ $products->previousPageUrl() }}">
        @endif

        @if($products->nextPageUrl())
            <link rel="next" href="{{ $products->nextPageUrl() }}">
        @endif
    @endif


@endsection

@section('title', settings('app.app_title').' - '.$category->title)

@section('main_content')
    <div class="row">
        <div class="col-xs-12 col-sm-12 {{ $products->currentPage() == 1 && $category->short_description ? ' col-md-8 ' : ' col-md-10 product-list-without-description ' }} col-md-push-2">
            <div class="row">
                <div class="col-sm-12 text-center category-page-header">
                    <div class="category-navigation">
                        <div class="category-tree">
                            <ol class="breadcrumb" vocab="https://schema.org/" typeof="BreadcrumbList">
                                <li property="itemListElement" typeof="ListItem">
                                    <a property="item" typeof="WebPage" href="{{ route('homepage') }}" title="{{ trans('main.old_category.home') }}">
                                        <span property="name" position="1">{{ trans('main.old_category.home') }}</span>
                                    </a>
                                </li>
                                @foreach($category->categoryTree() as $key=>$tree)
                                    <li property="itemListElement" typeof="ListItem">
                                        <a property="item" typeof="WebPage"  href="{{ prefixed_route($tree['link']) }}" title="{{ $tree['title'] }}">
                                            <span property="name">{{ $tree['title'] }}</span>
                                            <meta name="position" content="{{ $key + 2 }}" />
                                        </a>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                        <h1 class="category-page-title">
                            {{ ucfirst($category->title) }}
                        </h1>
                    </div>
                    <div class="filters">
                        <div class="total pull-left">
                            {{ $products->total().trans('main.old_category.products') }}
                        </div>
                        <div class="total pull-right">
                            <div class="dropdown sorting-dropdown">
                                <a class="sorting-toggle dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    {{ trans('main.old_category.sorted_by') }}
                                    @if ($sorting_field == 'popularity')
                                        {{ trans('main.old_category.sorted_by') }}
                                    @elseif ($sorting_field == 'title')
                                        {{ trans('main.old_category.name') }}
                                    @elseif ($sorting_field == 'price' && $sorting_direction == 'DESC')
                                        {{ trans('main.old_category.prices_more_expensive') }}
                                    @elseif ($sorting_field == 'price' && $sorting_direction == 'ASC')
                                        {{ trans('main.old_category.name') }}
                                    @endif
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <li><a data-sort-field="popularity" data-sort-direction="DESC" href="#">{{ trans('main.old_category.sorted_by') }}</a></li>
                                    <li><a data-sort-field="price" data-sort-direction="ASC"  href="#">{{ trans('main.old_category.prices_more_expensive') }}</a></li>
                                    <li><a data-sort-field="price" data-sort-direction="DESC" href="#">{{ trans('main.old_category.prices_more_expensive') }}</a></li>
                                    <li><a data-sort-field="title" data-sort-direction="ASC"  href="#">{{ trans('main.old_category.name') }}</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="top-filters-wrapper" data-filters-source="category_page" data-filters-source-id="{{ $category->id }}">
                        <div class="filter" data-tag-name="Marca">
                            <div class="filter-content">
                                {{ trans('main.old_category.brand') }}
                                <i class="fa fa-chevron-down"></i>
                            </div>
                            <div class="filter-dropdown">
                                <div class="scrollbar-rail filter-scroll">
                                </div>
                            </div>
                        </div>
                        <div class="filter" data-tag-name="Precio">
                            <div class="filter-content">
                              {{ trans('main.old_category.price') }}
                                <i class="fa fa-chevron-down"></i>
                            </div>
                            <div class="filter-dropdown">
                                <div class="filter-dropdown-content"></div>
                            </div>
                        </div>
                        @foreach($category_filters as $filter)
                            <div class="filter" data-tag-name="{{ $filter }}">
                                <div class="filter-content">
                                    {{ $filter }}
                                    <i class="fa fa-chevron-down"></i>
                                </div>
                                <div class="filter-dropdown">
                                    <div class="scrollbar-rail filter-scroll"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <hr>
                </div>
            </div>
            <!-- BEGIN PRODUCT LIST -->
            <div class="row">
                <div class="col-sm-12">
                    {{--<div class="row">--}}
                    <div class="products-list">
                        @forelse($products as $product)
                            <div class="product-tile-wrapper" data-product-url="{{ prefixed_route($product->url_key) }}">
                                <div class="product-tile" >
                                    <div class="product-discounts">
                                        @if ($product->coupon_url)
                                            <a class="coupon" href="{{ $product->coupon_url }}">{{ trans('main.old_category.coupon') }}</a>
                                        @endif
                                        @if ($product->previous_price and $product->previous_price != $product->price)
                                            <span class="discount">{{ round(100 - ( floatval($product->price) * 100 / floatval($product->previous_price))). '%' }}</span>
                                        @endif
                                    </div>
                                    <div class="product-image">
                                        <div class="image-async-loader" data-src="{{ resized_image($product->thumbnail) }}" data-alt="{{ $product->title }}"></div>
                                        <div class="overlay">
                                            @if (!isset($hide_buttons))
                                                <a class="first wishlist {{ isset($product->on_wishlist) || isset($wishlist_is_the_source) ? 'on-wishlist def-btn' : 'prim-btn' }} {{ isset($wishlist_is_the_source) ? 'remove_on_toggled' : '' }}" href="#" data-url="{{ route('wishlist_item') }}" data-pi="{{ $product->id }}" class="favorite-link"><i class="fa fa-heart fa-2x"></i></a>
                                                <form action="{{ route('product_shop') }}" method="post" target="_blank">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}" />
                                                    <button class="second prim-btn" rel="nofollow" target="_blank" class="product-file-link"><span>{{ trans('main.old_category.see_products') }}</span>  <i class="fa fa-chevron-right"></i></button>
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
                            <p class="text-center" style="margin-top: 2em;">
                                @if(count($filters) > 0)
                                    {{ trans('main.old_category.no_products_filters') }}
                                @else
                                    {{ trans('main.old_category.no_products') }}
                                @endif
                            </p>
                        @endforelse
                    </div>
                    {{--</div>--}}
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            {!! $products->render() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PRODUCT LIST -->
        <div class="col-xs-12 col-sm-12 col-md-2 sidebar {{ $products->currentPage() == 1 && $category->short_description ? ' col-md-pull-8 ' : ' col-md-pull-10 ' }} ">
            {{-- Category navigation tree shows all siblings and children of the current category --}}
            <div class="sidebar-navigation-wrapper">
                <h3 class="text-center visible-sm-block visible-xs-block">{{ trans('main.old_category.other_categories') }}</h3>
                @if($category->parent)
                    <h3>{{ $category->parent->title }}</h3>
                @endif
                <ul class="subcategories">
                    @foreach($category->siblings() as $sib)
                        <li @if($sib->id == $category->id) class="active" @endif>
                            <a class="sidebar-link {{ $sib->id == $category->id ? 'active' : '' }}" href="{{ prefixed_route($sib->url_key) }}">{{ $sib->title }}</a>
                            @if($sib->id == $category->id && $category->children)
                                <ul class="children">
                                    @foreach($category->visibleChildren as $child)
                                        <li @if($child->id == $category->id) class="active" @endif>
                                            <a class="sidebar-link" href="{{ prefixed_route($child->url_key) }}">{{ $child->title }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
            {{--<form action="" method="get" id="filter-form">--}}
            {{--<input type="hidden" name="sorting_field" id="sorting-field" value="{{ $sorting_field }}" />--}}
            {{--<input type="hidden" name="sorting_direction" id="sorting-direction" value="{{ $sorting_direction }}" />--}}
            {{--<div class="filters-wrapper">--}}
            {{--<div class="row">--}}
            {{--<div class="col-xs-12">--}}
            {{--<h3 style="margin-bottom: 10px;">{{ trans('main.old_category.filtered') }}</h3>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<div class="row">--}}
            {{--<div class="col-xs-12">--}}
            {{--<div class="row">--}}
            {{--<div class="col-xs-12">--}}
            {{--<h4>Precio</h4>--}}
            {{--</div>--}}
            {{--<div class="col-xs-12">--}}
            {{--<label class="filter-label" for="price_group_1"><input class="small-icheck" type="checkbox" name="price_range[]" id="price_group_1" value="0-10" {{ isset($filters['price_range']) && in_array('0-10', $filters['price_range']) ? 'checked="checked"' : '' }}> {{ trans('main.old_category.less_than_10) }} &euro;</label>--}}
            {{--</div>--}}
            {{--<div class="col-xs-12">--}}
            {{--<label class="filter-label" for="price_group_2"><input class="small-icheck" type="checkbox" name="price_range[]" id="price_group_2" value="10-20" {{ isset($filters['price_range']) && in_array('10-20', $filters['price_range']) ? 'checked="checked"' : '' }}> 10 &euro; {{ trans('main.old_category.to) }} 20&euro;</label>--}}
            {{--</div>--}}
            {{--<div class="col-xs-12">--}}
            {{--<label class="filter-label" for="price_group_3"><input class="small-icheck" type="checkbox" name="price_range[]" id="price_group_3" value="20-50" {{ isset($filters['price_range']) && in_array('20-50', $filters['price_range']) ? 'checked="checked"' : '' }}> 20 &euro; {{ trans('main.old_category.to) }} 50&euro;</label>--}}
            {{--</div>--}}
            {{--<div class="col-xs-12 price-filter">--}}
            {{--<input type="number" name="min_price" id="" class="form-control" placeholder="Min." value="{{ isset($filters['min_price']) && $filters['min_price'] ? $filters['min_price'] : old('filters[min_price]') }}"/>--}}
            {{--<span>-</span>--}}
            {{--<input type="number" name="max_price" id="" class="form-control" placeholder="Max." value="{{ isset($filters['max_price']) && $filters['max_price'] ? $filters['max_price'] : old('filters[max_price]') }}"/>--}}
            {{--<button type="submit"><i class="fa fa-filter"></i></button>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--@if (isset($brand_filters))--}}
            {{--<h4>{{ trans('main.old_category.brand) }}</h4>--}}
            {{--<ul class="filter-list see-more-filter-list">--}}
            {{--{{ dump($brand_filters, $filters) }}--}}
            {{--@foreach($brand_filters as $key=>$filter)--}}
            {{--@if ($filter->property_value)--}}
            {{--<li>--}}
            {{--<label class="filter-label" for="{{ 'brand_filter_'.$key }}">--}}
            {{--<input class="small-icheck filter-check"--}}
            {{--type="checkbox"--}}
            {{--name="filters[Marca][]"--}}
            {{--id="{{ 'brand_filter_'.$key }}"--}}
            {{--{{ isset($filters['Marca']) && in_array($filter->property_value, $filters['Marca']) ? 'checked="checked"' : '' }}--}}
            {{--value="{{ $filter->property_value }}">{{ $filter->property_value }} ({{ $filter->match_amount }})--}}
            {{--</label>--}}
            {{--</li>--}}
            {{--@endif--}}
            {{--@endforeach--}}
            {{--</ul>--}}
            {{--@endif--}}
            {{--@if ($available_filters)--}}
            {{--@foreach($available_filters as $out_key=>$filter)--}}
            {{--<h4>{{ $filter['filter_name'] }}</h4>--}}
            {{--<ul class="filter-list see-more-filter-list">--}}
            {{--@foreach($filter['filter_values'] as $key=>$filter_value)--}}
            {{--@if ($filter_value['property_value'])--}}
            {{--<li>--}}
            {{--<label for="{{ 'filter_'.$out_key.$key }}">--}}
            {{--<input class="small-icheck filter-check"--}}
            {{--type="checkbox"--}}
            {{--name="filters[{{ $filter['filter_name'] }}][]"--}}
            {{--id="{{ 'filter_'.$out_key.$key}}"--}}
            {{--{{ isset($filters[$filter['filter_name']]) && in_array($filter_value['property_value'], $filters[$filter['filter_name']]) ? 'checked="checked"' : '' }}--}}
            {{--value="{{ $filter_value['property_value'] }}"> {{ $filter_value['property_value'] }} ({{ $filter_value['match_amount'] }})--}}
            {{--</label>--}}
            {{--</li>--}}
            {{--@endif--}}
            {{--@endforeach--}}
            {{--</ul>--}}
            {{--@endforeach--}}
            {{--@endif--}}
            {{--</div>--}}
            {{--</form>--}}
        </div>
        @if ($products->currentPage() == 1 && $category->short_description)
            <div class="col-xs-12 col-sm-12 col-md-2">
                <div class="category-description-wrapper">
                    {!! $category->short_description !!}
                </div>
            </div>
        @endif
    </div>
@endsection

@section('javascripts')
    <script>
        $(function(){
            $('.top-filters-wrapper').siteFilters({
                source: $('.top-filters-wrapper').attr('data-filters-source'),
                source_id: $('.top-filters-wrapper').attr('data-filters-source-id'),
            });
        });
    </script>
@endsection