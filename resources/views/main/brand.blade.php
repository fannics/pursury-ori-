@extends('master')

@section('metadata')

    @if (!$brand->meta_noindex)
        <meta NAME="robots" CONTENT="index, follow">
    @else
        <meta NAME="robots" CONTENT="noindex, nofollow">
    @endif
    <meta name="description" content="{{ $brand->meta_description }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ settings('app.app_title').' - '.$brand->meta_title }}" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="{{ asset(settings('app.route_prefix').'/images/watermarksincom_resized.png') }}" />
    <meta property="og:description" content="{{ $brand->short_description }}" />
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

@section('title', settings('app.app_title').' - '.$brand->meta_title)

@section('main_content')
    <div class="row">
        <div class="col-xs-12 col-sm-12 {{ $products->currentPage() == 1 && $brand->short_description ? ' col-md-8 ' : ' col-md-10 product-list-without-description ' }} col-md-push-2">
            <div class="row">
                <div class="col-sm-12 text-center category-page-header">
                    <div class="category-navigation">
                        <div class="category-tree">
                            <ol class="breadcrumb" vocab="https://schema.org/" typeof="BreadcrumbList">
                                <li property="itemListElement" typeof="ListItem">
                                    <a property="item" typeof="WebPage" href="{{ route('homepage') }}" title="{{ trans('main.category.category_start') }}">
                                        <span property="name" position="1">{{ trans('main.category.category_start') }}</span>
                                    </a>
                                </li>
                                {{--@foreach($category->categoryTree() as $key=>$tree)--}}
                                    {{--<li property="itemListElement" typeof="ListItem">--}}
                                        {{--<a property="item" typeof="WebPage"  href="{{ prefixed_route($tree['link']) }}" title="{{ $tree['title'] }}">--}}
                                            {{--<span property="name">{{ $tree['title'] }}</span>--}}
                                            {{--<meta name="position" content="{{ $key + 2 }}" />--}}
                                        {{--</a>--}}
                                    {{--</li>--}}
                                {{--@endforeach--}}
                            </ol>
                        </div>
                        <h1 class="category-page-title">
                            {{ ucfirst($brand->title) }}
                        </h1>
                    </div>

                    <div class="clearfix"></div>
                    <div class="row visible-xs visible-sm">
                        <div class="col-xs-12">
                            <hr class="mobile-cp-hr" />
                        </div>
                    </div>
                    <div class="responsive-filters visible-xs visible-sm mobile-filter-row">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="mob-top-products-button-wrapper first">
                                    <button class="mob-top-products-button collapsed" type="button" data-toggle="collapse" data-target="#categoryCollapse" aria-expanded="false" aria-controls="collapseExample">
                                        {{ trans('main.category.category_categories') }}
                                        <i class="fa fa-chevron-down"></i>
                                        <i class="fa fa-chevron-up"></i>
                                    </button>
                                </div>
                                <div class="mob-top-products-button-wrapper last">
                                    <button class="mob-top-products-button collapsed" type="button" data-toggle="collapse" data-target="#filterCollapse" aria-expanded="false" aria-controls="collapseExample">
                                        {{ trans('main.category.category_filters') }}
                                        <i class="fa fa-chevron-down"></i>
                                        <i class="fa fa-chevron-up"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="category-collapse collapse" id="categoryCollapse">
                                @foreach($categories as $category)
                                    <li>
                                        <a class="sidebar-link {{ $category->parent_id == null ? 'text-uppercase' : '' }}" href="{{ Request::url() .'?category=' . $category->id }}" >@if(Request::input('category') == $category->id)<strong>{{$category->title}}</strong> @else{{ $category->title }}@endif</a>

                                    </li>
                                @endforeach
                            </div>

                        </div>
                    </div>
                    {{--<hr class="hidden-xs hidden-sm">--}}
                </div>
            </div>
            <!-- BEGIN PRODUCT LIST -->
            <div class="row">
                <div class="col-sm-12">
                    {{--<div class="row">--}}
                    <div class="products-list">
                        @forelse($products as $product)
                            @include('main/product_tile', ['product' => $product])
                        @empty
                            <p class="text-center" style="margin-top: 2em;">

                                    {{ trans('main.brands.brand_no_products') }}

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
        <div class="col-xs-12 col-sm-12 col-md-2 sidebar {{ $products->currentPage() == 1 && $brand->short_description ? ' col-md-pull-8 ' : ' col-md-pull-10 ' }} visible-md visible-lg ">
            {{-- Category navigation tree shows all siblings and children of the current category --}}
            <div class="sidebar-navigation-wrapper">
                <h3 class="text-center visible-sm-block visible-xs-block">{{ trans('main.category.category_other_categories') }}</h3>
                <ul class="subcategories">
                    @foreach($categories as $category)
                        <li>
                            <a class="sidebar-link {{ $category->parent_id == null ? 'text-uppercase' : '' }}" href="{{ Request::url() .'?category=' . $category->id }}" >@if(Request::input('category') == $category->id)<strong>{{$category->title}}</strong> @else{{ $category->title }}@endif</a>

                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @if ($products->currentPage() == 1 && $brand->short_description)
            <div class="col-xs-12 col-sm-12 col-md-2">
                <div class="category-description-wrapper">
                    @if(file_exists(public_path('images/brands/image') . '/' . $brand->image) && (!is_null($brand->image)))
                        <img style="width:50px; height: 50px" src="{{asset('images/brands/image') . '/'. $brand->image}}">

                    @endif
                    <br>
                     {!! $brand->short_description !!}
                </div>
            </div>
        @endif
    </div>
@endsection

