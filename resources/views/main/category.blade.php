@extends('master')

@section('metadata')

    @if (!$category->meta_no_index)
        <meta NAME="robots" CONTENT="index, follow">
    @else
        <meta NAME="robots" CONTENT="noindex, nofollow">
    @endif
    <meta name="description" content="{{ $category->meta_description }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ settings('app.app_title').' - '.$category->meta_title }}" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="{{ asset(settings('app.route_prefix').'/images/watermarksincom_resized.png') }}" />
    <meta property="og:description" content="{{ $category->short_description }}" />
    <meta property="og:site_name" content="{{ settings('app.app_title') }}" />
    
    <!-- HrefLang tags -->
    @foreach($hrefLangCategories as $hrefLangCategory)
      <link rel="alternate" hreflang="{{ $hrefLangCategory->language }}-{{ $hrefLangCategory->country }}" href="{{ url('') }}{{ settings('app.route_prefix') }}/{{ $hrefLangCategory->country }}/{{ $hrefLangCategory->language_url }}{{ $hrefLangCategory->url_key }}" />
    @endforeach
    
    
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

@section('title', settings('app.app_title').' - '.$category->meta_title)

@section('main_content')
    <div class="row">
        <div class="col-xs-12 col-sm-12 {{ $products->currentPage() == 1 && $category->short_description ? ' col-md-8 ' : ' col-md-10 product-list-without-description ' }} col-md-push-2">
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
                        <div class="category-products-total total pull-left">
                            {{ $products->total().' '.trans('main.category.product_count')}}
                        </div>
                        <div class="total pull-right hidden-xs hidden-sm">
                            <div class="dropdown sorting-dropdown">
                                <a class="sorting-toggle dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    {{ trans('main.category.category_sorted_by') }}
                                    @if ($sorting_field == 'popularity')
                                        {{ trans('main.category.category_sort_popularity') }}
                                    @elseif ($sorting_field == 'title')
                                        {{ trans('main.category.category_sort_name') }}
                                    @elseif ($sorting_field == 'price' && $sorting_direction == 'DESC')
                                        {{ trans('main.category.category_sort_expensive') }}
                                    @elseif ($sorting_field == 'price' && $sorting_direction == 'ASC')
                                        {{ trans('main.category.category_sort_cheaper') }}
                                    @endif
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <li><a data-sort-field="popularity" data-sort-direction="DESC" href="#">{{ trans('main.category.category_sort_popularity') }}</a></li>
                                    <li><a data-sort-field="price" data-sort-direction="ASC"  href="#">{{ trans('main.category.category_sort_cheaper') }}</a></li>
                                    <li><a data-sort-field="price" data-sort-direction="DESC" href="#">{{ trans('main.category.category_sort_expensive') }}</a></li>
                                    <li><a data-sort-field="title" data-sort-direction="ASC"  href="#">{{ trans('main.category.category_sort_name') }}</a></li>
                                </ul>
                            </div>
                        </div>
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
                                <ul class="subcategories">
                                    @foreach($category_tree as $category_tree_elem)
                                        <li>
                                            <a class="sidebar-link" href="{{ prefixed_route($category_tree_elem->url_key) }}">{{ $category_tree_elem->title }}</a>
                                            @if (isset($category_tree_elem->children) && count($category_tree_elem->children) > 0)
                                                <ul class="children">
                                                    @foreach($category_tree_elem->children as $children)
                                                        <li>
                                                            <a class="sidebar-link {{ $children->id == $category->id ? 'active' : '' }}" href="{{ prefixed_route($children->url_key) }}">{{ $children->title }}</a>
                                                            @if (isset($children->children) && count($children->children) > 0)
                                                                <ul class="children">
                                                                    @foreach($children->children as $ll_children)
                                                                        <li><a class="sidebar-link {{ $ll_children->id == $category->id ? 'active' : '' }}" href="{{ prefixed_route($ll_children->url_key) }}">{{ $ll_children->title }}</a></li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="filter-collapse collapse" id="filterCollapse">
                                <div class="top-filters-wrapper" data-filters-source="category_page" data-filters-source-id="{{ $category->id }}">
                                    <div class="filter visible-xs visible-sm" data-tag-name="Sorting">
                                        <div class="filter-content">
                                            <span>
                                                {{ trans('main.category.category_sorted_by') }}
                                                @if ($sorting_field == 'popularity')
                                                    {{ trans('main.category.category_sort_popularity') }}
                                                @elseif ($sorting_field == 'title')
                                                    {{ trans('main.category.category_sort_name') }}
                                                @elseif ($sorting_field == 'price' && $sorting_direction == 'DESC')
                                                    {{ trans('main.category.category_sort_cheaper') }}
                                                @elseif ($sorting_field == 'price' && $sorting_direction == 'ASC')
                                                    {{ trans('main.category.category_sort_expensive') }}
                                                @endif
                                            </span>
                                            <i class="fa fa-chevron-down"></i>
                                        </div>
                                        <div class="filter-dropdown filter-ready">
                                            <div class="filter-dropdown-content">
                                                <ul class="filter-tag-list">
                                                    <li data-sort-field="popularity" data-sort-direction="DESC" href="#">{{ trans('main.category.category_sort_popularity') }}</li>
                                                    <li data-sort-field="price" data-sort-direction="ASC"  href="#">{{ trans('main.category.category_sort_cheaper') }}</li>
                                                    <li data-sort-field="price" data-sort-direction="DESC" href="#"> {{ trans('main.category.category_sort_expensive') }}</li>
                                                    <li data-sort-field="title" data-sort-direction="ASC"  href="#">{{ trans('main.category.category_sort_name') }}</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="filter" data-tag-name="Marca">
                                        <div class="filter-content">
                                            @if (isset($used_filters['brand']))
                                                <span>{{ trans('main.category.filter_brand') }}: {{ implode(', ', $used_filters['brand']) }}</span>
                                            @else
                                                <span>{{ trans('main.category.filter_brand') }}</span>
                                            @endif
                                            <i class="fa fa-chevron-down"></i>
                                        </div>
                                        <div class="filter-dropdown">
                                            @if (isset($used_filters['brand']))
                                                <div class="used-filters">
                                                    <ul class="used-filters-list">
                                                        @foreach($used_filters['brand'] as $uf)
                                                            <li data-filter-label="brand" data-filter-id="{{ $uf }}"><i class="fa fa-times"></i> {{ $uf }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                            <div class="filter-search">
                                                <input type="text" class="filter-search-input" />
                                            </div>
                                            <div class="scrollbar-rail filter-scroll">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="filter" data-tag-name="Precio">
                                        <div class="filter-content">
                                            <span>{{ trans('main.category.filter_price') }}</span>
                                            <i class="fa fa-chevron-down"></i>
                                        </div>
                                        <div class="filter-dropdown">
                                            <div class="filter-dropdown-content" {{ isset($used_filters['price']) ? 'data-starting-price="'.$used_filters['price'].'"' : '' }}></div>
                                        </div>
                                    </div>
                                    @foreach($category_filters as $filter)
                                        @if($filter)
                                            <div class="filter" data-tag-name="{{ $filter }}">
                                                <div class="filter-content">
                                                    <span>{{ $filter }}{{ isset($used_filters[$filter]) && count($used_filters[$filter]) > 0 ? ':'.tag_list($used_filters[$filter]) : '' }}</span>
                                                    <i class="fa fa-chevron-down"></i>
                                                </div>
                                                <div class="filter-dropdown">
                                                    <div class="used-filters">
                                                        @if (isset($used_filters[$filter]))
                                                            <ul class="used-filters-list">
                                                                @foreach($used_filters[$filter] as $uf)
                                                                    <li data-filter-label="tag" data-filter-id="{{ $uf['tag_id'] }}"><i class="fa fa-times"></i> {{ $uf['tag_value'] }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                    <div class="filter-search">
                                                        <input type="text" class="filter-search-input" />
                                                    </div>
                                                    <div class="scrollbar-rail filter-scroll"></div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                    @if($used_filters && count($used_filters) > 0)
                                        <div class="filter remove-filters">
                                            <div class="filter-content">
                                                <span>{{ trans('main.category.filters_clear') }}</span>
                                                <i class="fa fa-times"></i>
                                            </div>
                                        </div>
                                    @endif
                                </div>
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
                                    @if(count($filters) > 0)
                                        {{ trans('main.category.category_no_products_filters') }}
                                    @else
                                        {{ trans('main.category.category_no_products') }}
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
        <div class="col-xs-12 col-sm-12 col-md-2 sidebar {{ $products->currentPage() == 1 && $category->short_description ? ' col-md-pull-8 ' : ' col-md-pull-10 ' }} visible-md visible-lg ">
            {{-- Category navigation tree shows all siblings and children of the current category --}}
            <div class="sidebar-navigation-wrapper">
                <h3 class="text-center visible-sm-block visible-xs-block">{{ trans('main.category.category_other_categories') }}</h3>
                <ul class="subcategories">
                    @foreach($category_tree as $category_tree_elem)
                        <li>
                            <a class="sidebar-link {{ $category_tree_elem->parent_id == null ? 'text-uppercase' : '' }}" href="{{ prefixed_route($category_tree_elem->url_key) }}">{{ $category_tree_elem->title }}</a>
                            @if (isset($category_tree_elem->children) && count($category_tree_elem->children) > 0)
                                <ul class="children">
                                    @foreach($category_tree_elem->children as $children)
                                        <li>
                                            <a class="sidebar-link {{ $children->id == $category->id ? 'active' : '' }}" href="{{ prefixed_route($children->url_key) }}">{{ $children->title }}</a>
                                            @if (isset($children->children) && count($children->children) > 0)
                                                <ul class="children">
                                                    @foreach($children->children as $ll_children)
                                                        <li><a class="sidebar-link {{ $ll_children->id == $category->id ? 'active' : '' }}" href="{{ prefixed_route($ll_children->url_key) }}">{{ $ll_children->title }}</a></li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
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