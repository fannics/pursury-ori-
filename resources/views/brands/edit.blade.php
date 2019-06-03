@extends('admin')

@section('main_content')

    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <h2>{{ trans('brands.form.edit_brand') }}</h2>
                            <p>
                                {{ trans('brands.form.complete_fields') }}
                            </p>
                        </div>
                    </div>
                    @if ($errors->count() > 0)
                        <div class="alert alert-danger">
                            {{ trans('brands.form.form_contains_errors') }}
                        </div>
                    @endif
                    <form class="form-horizontal" action="{{route('admin.brands.update',$brand->id)}}" method="post" id="product-form" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <fieldset>
                            <legend>{{ trans('brands.form.brand_data') }}</legend>
                            <div class="form-group">
                                <label for="product-title" class="col-sm-2 control-label">{{ trans('brands.form.title') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="product-title" name="title" value="{{ $errors->count() > 0 ? old('title') : $brand->title }}">
                                    @if ($errors->has('title'))
                                        <label for="product-title-error" class="error">
                                            {{ $errors->first('title') }}
                                        </label>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="brand_country" class="col-sm-2 control-label">{{ trans('brands.form.country') }}</label>
                                <div class="col-sm-4">
                                    <select class="form-control" id="brand_country" name="brand_country">
                                        @foreach($setups as $setup)
                                            <option {{ $brand->country == $setup->country_abre && $brand->language == $setup->language_abre  ? 'selected="selected"' : '' }} value="{{ $setup->id }}">{{ $setup->country }} / {{ $setup->language }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('brand_country'))
                                        <label for="brand_country-error" class="error">
                                            {{ $errors->first('brand_country') }}
                                        </label>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="product-name" class="col-sm-2 control-label">{{ trans('brands.form.name') }}</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $errors->count() > 0 ? old('name') : $brand->name }}">
                                    @if ($errors->has('name'))
                                        <label for="name-error" class="error">
                                            {{ $errors->first('name') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="url_key" class="col-sm-2 control-label">{{ trans('product.form.inner_url') }} <i class="required">*</i></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="url_key-url" name="url_key" value="{{ $errors->count() > 0 ? old('url_key') : stripslashes($brand->url_key) }}">
                                    @if ($errors->has('url_key'))
                                        <label for="url_key-error" class="error">
                                            {{ $errors->first('url_key') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                             <div class="form-group">
                                <label for="short_description" class="col-sm-2 control-label">{{ trans('brands.form.short_description') }}</label>
                                <div class="col-sm-6">
                                    <textarea rows="5" class="form-control" id="short_description" name="short_description">{{ $errors->count() > 0 ? old('short_description') : $brand->short_description }}</textarea>
                                    @if ($errors->has('short_description'))
                                        <label for="short_description-error" class="error">
                                            {{ $errors->first('short_description') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description" class="col-sm-2 control-label">{{ trans('brands.form.description') }}</label>
                                <div class="col-sm-6">
                                    <textarea rows="5" class="form-control" id="description" name="description">{{ $errors->count() > 0 ? old('description') : $brand->description }}</textarea>
                                    @if ($errors->has('description'))
                                        <label for="description-error" class="error">
                                            {{ $errors->first('description') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="is_visible-visible" class="col-sm-2 control-label">{{ trans('brands.form.visible') }}</label>
                                <div class="checkbox col-sm-6">
                                    <input {{ $errors->count() > 0 ? (old('is_visible') == '1' ? 'checked="checked"' : '') : ($brand->is_visible == '1' ? 'checked="checked"' : '')  }} type="checkbox" id="is_visible-visible" name="is_visible" value="1" />
                                    @if ($errors->has('is_visible'))
                                        <label for="is_visible-error" class="error">
                                            {{ $errors->first('is_visible') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="image" class="col-sm-2 control-label">{{ trans('brands.form.image') }}</label>

                                <div class="col-sm-6">
                                    <input type="file" class="form-control" id="image" name="image" >

                                    @if ($errors->has('image'))
                                        <label id="image-error" for="image" class="error" >
                                            {{ $errors->first('image') }}
                                        </label>
                                    @endif
                                </div>
                                @if(file_exists(public_path('images/brands/image') . '/'.$brand->image) && ($brand->image != null) )
                                    <div class="col-sm-4">
                                        <img style="width:100px; height:100px;" src="{{asset('images/brands/image').'/'.$brand->image}}">
                                    </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="category-sorting" class="col-sm-2 control-label">{{ trans('brands.form.sort_by_default') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <select class="form-control" id="brands-sorting" name="default_sorting">
                                        @foreach([trans('brands.form.name'), trans('brands.form.id')] as $field)
                                            <option {{ $errors->count() > 0 ? (old('default_sorting') == $field ? 'selected="selected"' : '') : ($brand->default_sorting == $field ? 'selected="selected"' : '') }} value="{{ $field }}">{{ $field }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('default_sorting'))
                                        <label id="brands_sorting-error" for="brands-sorting" class="error" >
                                            {{ $errors->first('default_sorting') }}
                                        </label>
                                    @endif
                                </div>
                            </div>


                        </fieldset>
                        <fieldset>
                            <legend>{{ trans('brands.form.seo') }}</legend>
                            <div class="form-group">
                                <label for="product-meta-title" class="col-sm-2 control-label">{{ trans('brands.form.meta_title') }} <i class="required">*</i></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="brands-meta-title" name="meta_title" value="{{ $errors->count() > 0 ? old('meta_title') : $brand->meta_title }}">
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
                                    <textarea rows="5" class="form-control" name="meta_description" id="brands-meta-description" >{{ $errors->count() > 0 ? old('meta_description') : $brand->meta_description }}</textarea>
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
                                    <input {{ $errors->count() > 0 ? (old('meta_noindex') != '1' ? 'checked="checked"' : '') : ($brand->meta_noindex == '1' ? 'checked="checked"' : '')  }} type="checkbox" id="brands-meta-noindex" name="meta_noindex" value="1" />
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