@extends('admin')

@section('main_content')
    <div class="row dashboard">
        <div class="col-sm-12">
            <div class="panel">
                <div class="panel-body">
                    <h1>{{ trans('admin.index.dashboard') }}</h1>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="tile blue">
                                <div class="tile-header">
                                    <h3><i class="fa fa-sitemap"></i> {{ $total_categories }}</h3>
                                    <p>{{ trans('admin.index.categories') }}</p>
                                </div>
                                <div class="tile-footer">
                                    <a href="{{ route('admin_categories_list') }}">{{ trans('admin.index.more_info') }} <i class="fa fa-plus-circle"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="tile red">
                                <div class="tile-header">
                                    <h3><i class="fa fa-futbol-o"></i> {{ $total_products }}</h3>
                                    <p>{{ trans('admin.index.products') }}</p>
                                </div>
                                <div class="tile-footer">
                                    <a href="{{ route('admin_product_list') }}">{{ trans('admin.index.more_info') }} <i class="fa fa-plus-circle"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="tile green">
                                <div class="tile-header">
                                    <h3><i class="fa fa-users"></i> {{ $total_users }}</h3>
                                    <p>{{ trans('admin.index.users') }}</p>
                                </div>
                                <div class="tile-footer">
                                    <a href="{{ route('admin_users') }}">{{ trans('admin.index.more_info') }} <i class="fa fa-plus-circle"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="tile orange">
                                <div class="tile-header">
                                    <h3><i class="fa fa-history"></i> {{ $total_imports }}</h3>
                                    <p>{{ trans('admin.index.imports') }}</p>
                                </div>
                                <div class="tile-footer">
                                    <a href="{{ route('admin_imports_done') }}">{{ trans('admin.index.more_info') }} <i class="fa fa-plus-circle"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="panel block-panel">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h3>{{ trans('admin.index.quick_tools') }}</h3>
                                    <div class="row quicktools">
                                        <div class="col-sm-3 text-center">
                                            <a href="{{ route('admin_products_update_routes') }}" data-confirm-message="{{ trans('admin.index.remake_routes_confirm') }}" class="global-index-action">
                                                <i class="fa fa-bicycle fa-5x" data-regular-icon="fa-bicycle"></i>
                                                <br>
                                                <p>{{ trans('admin.index.product_routes') }}</p>
                                            </a>
                                        </div>
                                        <div class="col-sm-3 text-center">
                                            <a href="{{ route('admin_categories_update_routes') }}" data-confirm-message="{{ trans('admin.index.remake_routes_confirm2') }}" class="global-index-action">
                                                <i class="fa fa fa-level-down fa-5x" data-regular-icon="fa-level-down"></i>
                                                <br>
                                                <p>{{ trans('admin.index.category_routes') }}</p>
                                            </a>
                                        </div>
                                        <div class="col-sm-3 text-center">
                                            <a href="{{ route('search_engine_refresh') }}" data-confirm-message="{{ trans('admin.index.reload_search_confirm') }}" class="global-index-action">
                                                <i class="fa fa-search fa-5x" data-regular-icon="fa-search"></i>
                                                <br>
                                                <p>{{ trans('admin.index.search_engine') }}</p>
                                            </a>
                                        </div>
                                        <div class="col-sm-3 text-center">
                                            <a href="{{ route('admin_show_logs') }}">
                                                <i class="fa fa-server fa-5x"></i>
                                                <br>
                                                <p>{{ trans('admin.index.errors_log') }}</p>
                                            </a>
                                        </div>
                                        <div class="col-sm-3 text-center">
                                            <a href="{{ route('admin_users') }}">
                                                <i class="fa fa-users fa-5x"></i>
                                                <br>
                                                <p>{{ trans('admin.index.users') }}</p>
                                            </a>
                                        </div>
                                        <div class="col-sm-3 text-center">
                                            <a href="{{ route('admin_categories_update_tree_cache') }}" data-confirm-message="{{ trans('admin.index.restart_cache_confirm') }}" class="global-index-action">
                                                <i class="fa fa-puzzle-piece fa-5x" data-regular-icon="fa-puzzle-piece"></i>
                                                <br>
                                                <p>{{ trans('admin.index.restart_categories_cache') }}</p>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="panel block-panel">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h3>{{ trans('admin.index.latest_users') }}</h3>
                                    <table class="table table-condensed">
                                        <thead>
                                        <tr>
                                            <th>{{ trans('admin.index.name') }}</th>
                                            <th>{{ trans('admin.index.email') }}</th>
                                            <th>{{ trans('admin.index.date') }}</th>
                                        </tr>
                                        </thead>
                                        @foreach($latest_users as $user)
                                            <tr>
                                                <td>{{ $user['name'] }}</td>
                                                <td>{{ $user['email'] }}</td>
                                                <td>{{ $user['created_at'] }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel">
                        <div class="panel-body">
                            <h3>{{ trans('admin.index.most_viewed_products') }}</h3>
                            @if ($most_seen->count() > 0)
                                <div class="products-list index-list">
                                    @foreach($most_seen as $product)
                                        @include('admin/product_tile', ['product' => $product, 'per_row' => 4])
                                    @endforeach
                                </div>
                            @else
                                <p class="text-center">{{ trans('admin.index.no_visits_products') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection