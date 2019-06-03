@extends('admin')

@section('main_content')

    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12">
                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <strong>{{ trans('admin.settings.oops') }}</strong> {{ trans('admin.settings.errors') }}<br><br>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <h2>{{ trans('admin.settings.general_settings') }}</h2>
                            <form action="{{ route('admin_settings_post') }}" method="post" class="form-horizontal" id="settings-form">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                <fieldset>
                                    <legend>{{ trans('admin.settings.application') }}</legend>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-2 control-label" for="app_title">{{ trans('admin.settings.application_name') }}</label>
                                        <div class="col-xs-12 col-sm-6">
                                            <input class="form-control"  type="text" name="app_title" id="app_title" value="{{ settings('app.app_title') }}"/>
                                            <p class="help-block">{{ trans('admin.settings.title_hint') }}</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-2 control-label" for="route_prefix">{{ trans('admin.settings.routes_prefix') }}</label>
                                        <div class="col-xs-12 col-sm-6">
                                            <input class="form-control"  type="text" name="route_prefix" id="route_prefix" value="{{ settings('app.route_prefix') }}" />
                                            <p class="help-block">{{ trans('admin.settings.routes_prefix_hint') }}</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-xs-12 col-sm-6 col-sm-offset-2">
                                            <label for="debug">
                                                <input class="form-control" type="checkbox" name="debug" id="debug" {{ (bool)settings('app.debug') == true ? 'checked="checked"' : '' }} />
                                                {{ trans('admin.settings.enable_debugging') }}
                                            </label>
                                            <p class="help-block">{{ trans('admin.settings.enable_debugging_hint') }}</p>
                                        </div>
                                    </div>
                                </fieldset>
                                {{--<fieldset>--}}
                                    {{--<legend>Orden de productos</legend>--}}
                                    {{--<div class="form-group">--}}
                                        {{--<label for="product_order_limit" class="col-xs-12 col-sm-2 text-right">Limite de productos con orden dentro de la categoría</label>--}}
                                        {{--<div class="col-xs-12 col-sm-6">--}}
                                            {{--<input class="form-control" type="text" name="product_order_limit" id="product_order_limit" value="{{ settings('app.product_order_limit') ? settings('app.product_order_limit') : 20 }}" style="width:  160px;">--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</fieldset>--}}
                                <fieldset>
                                    <legend>{{ trans('admin.settings.search_engine') }}</legend>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-2 control-label" for="elasticsearch_host">{{ trans('admin.settings.elasticsearch_url') }}</label>
                                        <div class="col-xs-12 col-sm-6">
                                            <input class="form-control"  type="text" name="elasticsearch_host" id="elasticsearch_host" value="{{ settings('app.elasticsearch_host') }}"/>
                                            <p class="help-block">{{ trans('admin.settings.elasticsearch_point_access') }}</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-2 control-label" for="elasticsearch_index_name">{{ trans('admin.settings.index_name') }}</label>
                                        <div class="col-xs-12 col-sm-6">
                                            <input class="form-control"  type="text" name="elasticsearch_index_name" id="elasticsearch_index_name" value="{{ settings('app.elasticsearch_index_name') }}"/>
                                            <p class="help-block">{{ trans('admin.settings.index_name_hint') }}</p>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset>
                                    <legend>{{ trans('admin.settings.images_processing') }}</legend>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6 col-sm-offset-2">
                                            <p>{{ trans('admin.settings.images_processing_summary') }}</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-xs-12 col-sm-6 col-sm-offset-2">
                                            <div class="radio">
                                                <label for="image_engine_0">
                                                    <input class="image-engine-selector" type="radio" id="image_engine_0" name="image_processor" value="default" {{ settings('app.image_processor') == 'default' ? 'checked="checked"' : '' }} /> {{ trans('admin.settings.by_default') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-xs-12 col-sm-6 col-sm-offset-2">
                                            <div class="radio">
                                                <label for="image_engine_1">
                                                    <input class="image-engine-selector" type="radio" id="image_engine_1" name="image_processor" value="Images.weserv.nl" {{ settings('app.image_processor') == 'Images.weserv.nl' ? 'checked="checked"' : '' }} /> Images.weserv.nl
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-xs-12 col-sm-6 col-sm-offset-2">
                                            <div class="radio">
                                                <label for="image_engine_2">
                                                    <input class="image-engine-selector" type="radio" id="image_engine_2" name="image_processor" value="thumbor" {{ settings('app.image_processor') == 'thumbor' ? 'checked="checked"' : '' }} /> Thumbor
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group thumbor-url" style="{{ settings('app.image_processor') != 'thumbor' ? 'display:none' : '' }}">
                                        <label class="col-xs-12 col-sm-2 control-label" for="thumbor_address">{{ trans('admin.settings.thumbor_url') }}</label>
                                        <div class="col-xs-12 col-sm-6">
                                            <input class="form-control"  type="text" name="thumbor_address" id="thumbor_address" value="{{ settings('app.thumbor_address') }}"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-2 control-label" for="thumbnail_size_for_tile_width">{{ trans('admin.settings.thumbnails_dimensions') }}</label>
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <div class="col-xs-3">
                                                    <input class="form-control"  type="number" name="thumbnail_size_for_tile_width" id="thumbnail_size_for_tile_width" value="{{ extract_dimensions(settings('app.thumbnail_size_for_tile'))[0] }}"/>
                                                </div>
                                                <div class="col-xs-3">
                                                    @if (empty(extract_dimensions(settings('app.thumbnail_size_for_tile'))[1]))
                                                      <input class="form-control"  type="number" name="thumbnail_size_for_tile_height" id="thumbnail_size_for_tile_height" value=""/>
                                                    @else
                                                      <input class="form-control"  type="number" name="thumbnail_size_for_tile_height" id="thumbnail_size_for_tile_height" value="{{ extract_dimensions(settings('app.thumbnail_size_for_tile'))[1] }}"/>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-2 control-label" for="product_file_image_size_width">{{ trans('admin.settings.product_image_dimensions') }}</label>
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <div class="col-xs-3">
                                                    <input class="form-control"  type="number" name="product_file_image_size_width" id="product_file_image_size_width" value="{{ extract_dimensions(settings('app.product_file_image_size'))[0] }}"/>
                                                </div>
                                                <div class="col-xs-3">
                                                    @if (empty(extract_dimensions(settings('app.product_file_image_size'))[1]))
                                                      <input class="form-control"  type="number" name="product_file_image_size_height" id="product_file_image_size_height" value=""/>
                                                    @else
                                                      <input class="form-control"  type="number" name="product_file_image_size_height" id="product_file_image_size_height" value="{{ extract_dimensions(settings('app.product_file_image_size'))[1] }}"/>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <hr>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-6 col-sm-offset-2">
                                        <button type="submit" class="btn btn-primary">{{ trans('admin.settings.save') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection