@extends('admin')

@section('main_content')
    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h2>{{ trans('product.integrity_check.duplicated_products') }}</h2>
                            <p>
                                {{ trans('product.integrity_check.duplicated_products_shown') }}
                            </p>
                        </div>
                        <div class="col-xs-6 text-right">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <form action="{{ route('admin_products_batch') }}" method="post">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>{{ trans('product.integrity_check.name') }}</th>
                                        <th>{{ trans('product.integrity_check.url') }}</th>
                                        <th>{{ trans('product.integrity_check.price') }}</th>
                                        <th>{{ trans('product.integrity_check.visible') }}</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($dupes as $product)
                                        <tr>
                                            <td>{{ $product->title }}</td>
                                            <td><a href="{{ prefixed_route(stripslashes($product->url_key)) }}">{{ prefixed_route(stripslashes($product->url_key)) }}</a></td>
                                            <td>{{ number_format($product->price, 2) }}</td>
                                            <td>{!! $product->is_visible ? '<i class="fa fa-lg fa-check"></i>' : '<i class="fa fa-lg fa-ban"></i>' !!}</td>
                                            <td><a href="{{ route('admin_products_edit', ['id' => $product->id]) }}"><i class="fa fa-edit fa-lg"></i></a></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h2>{{ trans('product.integrity_check.products_no_categories') }}</h2>
                            <p>
                                {{ trans('product.integrity_check.products_no_categories_shown') }}
                            </p>
                        </div>
                        <div class="col-xs-6 text-right">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <form action="{{ route('admin_products_batch') }}" method="post">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>{{ trans('product.integrity_check.name') }}</th>
                                        <th>{{ trans('product.integrity_check.url') }}</th>
                                        <th>{{ trans('product.integrity_check.price') }}</th>
                                        <th>{{ trans('product.integrity_check.visible') }}</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($without_categories as $product)
                                        <tr>
                                            <td>{{ $product->title }}</td>
                                            <td><a href="{{ prefixed_route(stripslashes($product->url_key)) }}">{{ prefixed_route(stripslashes($product->url_key)) }}</a></td>
                                            <td>{{ number_format($product->price, 2) }}</td>
                                            <td>{!! $product->is_visible ? '<i class="fa fa-lg fa-check"></i>' : '<i class="fa fa-lg fa-ban"></i>' !!}</td>
                                            <td><a href="{{ route('admin_products_edit', ['id' => $product->id]) }}"><i class="fa fa-edit fa-lg"></i></a></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h2>{{ trans('product.integrity_check.products_missing_info') }}</h2>
                            <p>
                                {{ trans('product.integrity_check.products_missing_info_shown') }}
                            </p>
                        </div>
                        <div class="col-xs-6 text-right">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <form action="{{ route('admin_products_batch') }}" method="post">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>{{ trans('product.integrity_check.title') }}</th>
                                        <th>{{ trans('product.integrity_check.description') }}</th>
                                        <th>{{ trans('product.integrity_check.url') }}</th>
                                        <th>{{ trans('product.integrity_check.price') }}</th>
                                        <th>{{ trans('product.integrity_check.image') }}</th>
                                        <th>{{ trans('product.integrity_check.thumbnail') }}</th>
                                        <th>{{ trans('product.integrity_check.brand') }}</th>
                                        <th>{{ trans('product.integrity_check.target_url') }}</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($with_missing_fields as $product)
                                        <tr>
                                            <td>
                                                @if($product->title)
                                                    {{ $product->title }}
                                                @else
                                                    <span class="label label-danger"><i class="fa fa-ban"></i></span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->description)
                                                    <span class="label label-success"><i class="fa fa-check"></i></span>
                                                @else
                                                    <span class="label label-danger"><i class="fa fa-ban"></i></span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->url_key)
                                                    <span class="label label-success"><i class="fa fa-check"></i></span>
                                                @else
                                                    <span class="label label-danger"><i class="fa fa-ban"></i></span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->price)
                                                    <span class="label label-success"><i class="fa fa-check"></i></span>
                                                @else
                                                    <span class="label label-danger"><i class="fa fa-ban"></i></span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->image)
                                                    <span class="label label-success"><i class="fa fa-check"></i></span>
                                                @else
                                                    <span class="label label-danger"><i class="fa fa-ban"></i></span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->thumbnail)
                                                    <span class="label label-success"><i class="fa fa-check"></i></span>                
                                                @else
                                                    <span class="label label-danger"><i class="fa fa-ban"></i></span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->brand)
                                                    <span class="label label-success"><i class="fa fa-check"></i></span>
                                                @else
                                                    <span class="label label-danger"><i class="fa fa-ban"></i></span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->destination_url)
                                                    <span class="label label-success"><i class="fa fa-check"></i></span>
                                                @else
                                                    <span class="label label-danger"><i class="fa fa-ban"></i></span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin_products_edit', ['id' => $product->id]) }}"><i class="fa fa-edit fa-lg"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

