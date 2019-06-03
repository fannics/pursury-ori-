@extends('master')

@section('metadata')
    <meta NAME="robots" CONTENT="index, follow">
    <meta name="description" content="{{ trans('main.searchPage.search_results_seo_description') }}">

    <meta property="og:title" content="{{ settings('app.app_title').' - '.trans('main.searchPage.search_results')}}" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="{{ asset(settings('app.route_prefix').'/images/watermarksincom_resized.png') }}" />
    <meta property="og:description" content="{{ trans('main.searchPage.search_results') }}" />
    <meta property="og:site_name" content="{{ settings('app.app_title') }}" />
@endsection

@section('title', settings('app.app_title').' - '.trans('main.searchPage.search_results'))

@section('main_content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-sm-12 text-center category-page-header">
                    <div class="category-navigation">
                        <h2 class="category-page-title">
                            {{ trans('main.searchPage.search_results_for') }}: {{ truncate_str($term, 50) }}
                        </h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-4 col-sm-offset-4">
                    <form action="" method="get" id="filter-form">
                        <input type="hidden" name="sorting_field" id="sorting-field" value="{{ $sorting_field }}" />
                        <input type="hidden" name="sorting_direction" id="sorting-direction" value="{{ $sorting_direction }}">
                        <div class="input-group">
                            <input type="text" class="form-control" name="term" value="{{ $term }}" required placeholder="{{ trans('main.searchPage.search_results_placeholder') }}" />
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="filters">
                        <div class="total pull-left">
                            {{ $products->total().' '. trans('main.category.product_count') }}
                        </div>
                        <div class="total pull-right">
                            <div class="dropdown sorting-dropdown">
                                <a class="sorting-toggle dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    {{ trans('main.searchPage.category_sorted_by') }}
                                    @if ($sorting_field == 'popularity')
                                        {{ trans('main.searchPage.category_sort_popularity') }}
                                    @elseif ($sorting_field == 'title')
                                        {{ trans('main.searchPage.category_sort_name') }}
                                    @elseif ($sorting_field == 'price' && $sorting_direction == 'DESC')
                                        {{ trans('main.searchPage.category_sort_expensive') }}
                                    @elseif ($sorting_field == 'price' && $sorting_direction == 'ASC')
                                        {{ trans('main.searchPage.category_sort_cheaper') }}
                                    @endif 
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <li><a data-sort-field="popularity" data-sort-direction="DESC" href="#">{{ trans('main.searchPage.category_sort_popularity') }}</a></li>
                                    <li><a data-sort-field="price" data-sort-direction="ASC"  href="#">{{ trans('main.searchPage.category_sort_cheaper') }}</a></li>
                                    <li><a data-sort-field="price" data-sort-direction="DESC" href="#">{{ trans('main.searchPage.category_sort_expensive') }}</a></li>
                                    <li><a data-sort-field="title" data-sort-direction="ASC"  href="#">{{ trans('main.searchPage.category_sort_name') }}</a></li>
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
            <div class="products-list search-list">
                @if (count($products) > 0)
                    @foreach($products as $product)
                      @include('main/product_tile', ['product' => $product])
                    @endforeach
                @else
                    <p class="text-center">{{ trans('main.searchPage.no_search_results') }}</p>
                @endif
            </div>
            <div class="row">
                <div class="col-sm-12 text-center">
                    {!! $products->render() !!}
                </div>
            </div>
        </div>
    </div>
@endsection