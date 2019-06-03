@extends('admin')

@section('main_content')

    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <h2>{{ trans('stores.form.edit_brand') }}</h2>
                            <p>
                                {{ trans('stores.form.complete_fields') }}
                            </p>
                        </div>
                    </div>
                    @if ($errors->count() > 0)
                        <div class="alert alert-danger">
                            {{ trans('brands.form.form_contains_errors') }}
                        </div>
                    @endif
                    <form class="form-horizontal" action="{{route('admin.stores.update',$store->id)}}" method="post" id="product-form" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <fieldset>
                            <legend>{{ trans('stores.form.brand_data') }}</legend>
                            <div class="form-group">
                                <label for="product-title" class="col-sm-2 control-label">{{ trans('stores.form.name') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="product-title" name="name" value="{{ $errors->count() > 0 ? old('name') : $store->name }}">
                                    @if ($errors->has('name'))
                                        <label for="product-title-error" class="error">
                                            {{ $errors->first('name') }}
                                        </label>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="brand_country" class="col-sm-2 control-label">{{ trans('brands.form.country') }}</label>
                                <div class="col-sm-4">
                                    <select class="form-control" id="brand_country" name="stores_country">
                                        @foreach($setups as $setup)
                                            <option {{ $store->country == $setup->country_abre && $store->language == $setup->language_abre  ? 'selected="selected"' : '' }} value="{{ $setup->id }}">{{ $setup->country }} / {{ $setup->language }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('stores_country'))
                                        <label for="brand_country-error" class="error">
                                            {{ $errors->first('stores_country') }}
                                        </label>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="url_key" class="col-sm-2 control-label">{{ trans('product.form.inner_url') }} <i class="required">*</i></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="url_key-url" name="url_key" value="{{ $errors->count() > 0 ? old('url_key') : stripslashes($store->url_key) }}">
                                    @if ($errors->has('url_key'))
                                        <label for="url_key-error" class="error">
                                            {{ $errors->first('url_key') }}
                                        </label>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="is_visible-visible" class="col-sm-2 control-label">{{ trans('brands.form.visible') }}</label>
                                <div class="checkbox col-sm-6">
                                    <input {{ $errors->count() > 0 ? (old('is_visible') == '1' ? 'checked="checked"' : '') : ($store->is_visible == '1' ? 'checked="checked"' : '')  }} type="checkbox" id="is_visible-visible" name="is_visible" value="1" />
                                    @if ($errors->has('is_visible'))
                                        <label for="is_visible-error" class="error">
                                            {{ $errors->first('is_visible') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="image" class="col-sm-2 control-label">{{ trans('stores.form.logo') }}</label>

                                <div class="col-sm-6">
                                    <input type="file" class="form-control" id="image" name="logo" >

                                    @if ($errors->has('logo'))
                                        <label id="image-error" for="image" class="error" >
                                            {{ $errors->first('logo') }}
                                        </label>
                                    @endif
                                </div>
                                @if(file_exists(public_path('images/stores/logo') . '/'.$store->logo) && ($store->logo != null) )
                                    <div class="col-sm-4">
                                        <img style="width:100px; height:100px;" src="{{asset('images/stores/logo').'/'.$store->logo}}">
                                    </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="image" class="col-sm-2 control-label">{{ trans('stores.form.logo_thumb') }}</label>

                                <div class="col-sm-6">
                                    <input type="file" class="form-control" id="image" name="logo_thumb" >

                                    @if ($errors->has('logo_thumb'))
                                        <label id="image-error" for="image" class="error" >
                                            {{ $errors->first('logo_thumb') }}
                                        </label>
                                    @endif
                                </div>
                                @if(file_exists(public_path('images/stores/logo_thumb') . '/'.$store->logo_thumb) && ($store->logo_thumb != null) )
                                    <div class="col-sm-4">
                                        <img style="width:100px; height:100px;" src="{{asset('images/stores/logo_thumb').'/'.$store->logo_thumb}}">
                                    </div>
                                @endif
                            </div>




                        </fieldset>
                        <fieldset>
                            <legend>{{ trans('brands.form.seo') }}</legend>
                            <div class="form-group">
                                <label for="product-meta-title" class="col-sm-2 control-label">{{ trans('brands.form.meta_title') }} <i class="required">*</i></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="brands-meta-title" name="meta_title" value="{{ $errors->count() > 0 ? old('meta_title') : $store->meta_title }}">
                                    @if ($errors->has('meta_title'))
                                        <label for="brands_meta_title-error" class="error">
                                            {{ $errors->first('meta_title') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="brands-meta-description" class="col-sm-2 control-label">{{ trans('brands.form.meta_description') }} <i class="required">*</i></label>
                                <div class="col-sm-6">
                                    <textarea rows="5" class="form-control" name="meta_description" id="brands-meta-description" >{{ $errors->count() > 0 ? old('meta_description') : $store->meta_description }}</textarea>
                                    @if ($errors->has('meta_description'))
                                        <label for="brands_meta_description-error" class="error">
                                            {{ $errors->first('meta_description') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="brands-meta-noindex" class="col-sm-2 control-label">{{ trans('brands.form.no_index') }}</label>
                                <div class="checkbox col-sm-6">
                                    <input {{ $errors->count() > 0 ? (old('meta_noindex') != '1' ? 'checked="checked"' : '') : ($store->meta_noindex == '1' ? 'checked="checked"' : '')  }} type="checkbox" id="brands-meta-noindex" name="meta_noindex" value="1" />
                                </div>
                            </div>
                        </fieldset>
                        <div class="row">
                            <div class="col-sm-6 col-sm-offset-2">
                                <button type="submit" class="btn btn-primary">{{ trans('brands.form.save_changes') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection