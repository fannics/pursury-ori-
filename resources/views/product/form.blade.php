@extends('admin')

@section('main_content')

    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <h2>{{ trans('product.form.edit_product') }}</h2>
                            <p>
                                {{ trans('product.form.complete_fields') }}
                            </p>
                        </div>
                    </div>
                    @if ($errors->count() > 0)
                        <div class="alert alert-danger">
                            {{ trans('product.form.form_contains_errors') }}
                        </div>
                    @endif
                    <form class="form-horizontal" action="" method="post" id="product-form">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <fieldset>
                            <legend>{{ trans('product.form.product_data') }}</legend>
                            <div class="form-group">
                                <label for="product-title" class="col-sm-2 control-label">{{ trans('product.form.title') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="product-title" name="product_title" value="{{ $errors->count() > 0 ? old('product_title') : $product->title }}">
                                    @if ($errors->has('product_title'))
                                        <label for="product-title-error" class="error">
                                            {{ $errors->first('product_title') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-id" class="col-sm-2 control-label">{{ trans('product.form.product_id') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="product-id" name="product_id" value="{{ $errors->count() > 0 ? old('product_id') : $product->product_id }}">
                                    @if ($errors->has('product_id'))
                                        <label for="product-id-error" class="error">
                                            {{ $errors->first('product_id') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-parent-id" class="col-sm-2 control-label">{{ trans('product.form.parent_id') }}</label>
                                <div class="col-sm-4">
                                    <select class="form-control" id="product-parent-id" name="product_parent_id">
                                        <option value=''>NULL</option>
                                        @foreach($possibleParents as $pParent)
                                            <option {{ $product->parent_id == $pParent->product_id  ? 'selected="selected"' : '' }} value="{{ $pParent->product_id }}">{{ $pParent->title }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('product_parent_id'))
                                        <label for="product_parent_id-error" class="error">
                                            {{ $errors->first('product_parent_id') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-country" class="col-sm-2 control-label">{{ trans('product.form.country') }}</label>
                                <div class="col-sm-4">
                                    <select class="form-control" id="product-country" name="product_country">
                                        @foreach($setups as $setup)
                                            <option {{ $product->country == $setup->country_abre && $product->language == $setup->language_abre  ? 'selected="selected"' : '' }} value="{{ $setup->id }}">{{ $setup->country }} / {{ $setup->language }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('product_country'))
                                        <label for="product_country-error" class="error">
                                            {{ $errors->first('product_country') }}
                                        </label>
                                    @endif
                                </div>
                            </div>                          
                            <div class="form-group">
                                <label for="product-store" class="col-sm-2 control-label">{{ trans('product.form.store') }}</label>
                                <div class="col-sm-4">
                                    <select class="form-control" name="product_store">
                                        @foreach($stores as $store)
                                            <option value="{{$store->id}}" @if($product->store == $store->id) selected @endif>{{$store->name}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('product_store'))
                                        <label for="product_store-error" class="error">
                                            {{ $errors->first('product_store') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-brand" class="col-sm-2 control-label">{{ trans('product.form.brand') }}</label>
                                <div class="col-sm-4">
                                    <select class="form-control" name="product_brand">
                                        @foreach($brands as $brand)
                                            <option value="{{$brand->id}}" @if($product->brand == $brand->id) selected @endif>{{$brand->title}}</option>
                                            @endforeach
                                    </select>
                                    @if ($errors->has('product_brand'))
                                        <label for="product_brand-error" class="error">
                                            {{ $errors->first('product_brand') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-category" class="col-sm-2 control-label">{{ trans('product.form.category') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <select name="product_category[]" id="product-category" class="form-control" multiple="multiple">
                                        @foreach($categories as $cat)
                                            <option {{ $product->hasCategory($cat->id) ? 'selected="selected"' : '' }} value="{{ $cat->id }}">{{ $cat->title }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('product_category'))
                                        <label for="product_category-error" class="error">
                                            {{ $errors->first('product_category') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-url" class="col-sm-2 control-label">{{ trans('product.form.inner_url') }} <i class="required">*</i></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="product-url" name="product_url" value="{{ $errors->count() > 0 ? old('product_url') : stripslashes($product->url_key) }}">
                                    <p class="help-block">{{ trans('product.form.inner_url_hint') }}</p>
                                    @if ($errors->has('product_url'))
                                        <label for="product_url-error" class="error">
                                            {{ $errors->first('product_url') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-destination-url" class="col-sm-2 control-label">{{ trans('product.form.target_url') }} <i class="required">*</i></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="product-destination-url" name="product_destination_url" value="{{ $errors->count() > 0 ? old('product_destination_url') : $product->destination_url }}">
                                    <p class="help-block">{{ trans('product.form.target_url_hint') }}</p>
                                    @if ($errors->has('product_destination_url'))
                                        <label for="product_destination_url-error" class="error">
                                            {{ $errors->first('product_destination_url') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-short-description" class="col-sm-2 control-label">{{ trans('product.form.short_description') }}</label>
                                <div class="col-sm-6">
                                    <textarea rows="5" class="form-control" id="product-short-description" name="product_short_description">{{ $errors->count() > 0 ? old('product_short_description') : $product->short_description }}</textarea>
                                    @if ($errors->has('product_short_description'))
                                        <label for="product_short_description-error" class="error">
                                            {{ $errors->first('product_short_description') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-description" class="col-sm-2 control-label">{{ trans('product.form.description') }}</label>
                                <div class="col-sm-6">
                                    <textarea rows="5" class="form-control" id="product-description" name="product_description">{{ $errors->count() > 0 ? old('product_description') : $product->description }}</textarea>
                                    @if ($errors->has('product_description'))
                                        <label for="product_description-error" class="error">
                                            {{ $errors->first('product_description') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-visible" class="col-sm-2 control-label">{{ trans('product.form.visible') }}</label>
                                <div class="checkbox col-sm-6">
                                    <input {{ $errors->count() > 0 ? (old('product_visible') == '1' ? 'checked="checked"' : '') : ($product->is_visible == '1' ? 'checked="checked"' : '')  }} type="checkbox" id="product-visible" name="product_visible" value="1" />
                                    @if ($errors->has('product_visible'))
                                        <label for="product_visible-error" class="error">
                                            {{ $errors->first('product_visible') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-thumbnail" class="col-sm-2 control-label">{{ trans('product.form.thumbnail_url') }}</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="product-thumbnail" name="product_thumbnail" value="{{ $errors->count() > 0 ? old('product_thumbnail') : $product->thumbnail }}">
                                    @if ($errors->has('product_thumbnail'))
                                        <label for="product_thumbnail-error" class="error">
                                            {{ $errors->first('product_thumbnail') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-image" class="col-sm-2 control-label">{{ trans('product.form.image_url') }}</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="product-image" name="product_image" value="{{ $errors->count() > 0 ? old('product_image') : $product->image }}">
                                    @if ($errors->has('product_image'))
                                        <label for="product_image-error" class="error">
                                            {{ $errors->first('product_image') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-image-alt" class="col-sm-2 control-label">{{ trans('product.form.image_alt') }}</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="product-image-alt" name="product_image_alt" value="{{ $errors->count() > 0 ? old('product_image_alt') : $product->image_alt }}">
                                    @if ($errors->has('product_image_alt'))
                                        <label for="product_image_alt-error" class="error">
                                            {{ $errors->first('product_image_alt') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-price" class="col-sm-2 control-label">{{ trans('product.form.price') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="product-price" name="product_price" value="{{ $errors->count() > 0 ? old('product_price') : $product->price }}">
                                    @if ($errors->has('product_price'))
                                        <label for="product_price-error" class="error">
                                            {{ $errors->first('product_price') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-previous-price" class="col-sm-2 control-label">{{ trans('product.form.previous_price') }}</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="product-previous-price" name="product_previous_price" value="{{ $errors->count() > 0 ? old('product_previous_price') : $product->previous_price }}">
                                    <p class="help-block">{{ trans('product.form.previous_price_hint') }}</p>
                                    @if ($errors->has('product_previous_price'))
                                        <label for="product_previous_price-error" class="error">
                                            {{ $errors->first('product_previous_price') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-shipping-cost" class="col-sm-2 control-label">{{ trans('product.form.shipping_cost') }}</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="product-shipping-cost" name="product_shipping_cost" value="{{ $errors->count() > 0 ? old('product_shipping_cost') : $product->shipping_cost }}">
                                    @if ($errors->has('product_shipping_cost'))
                                        <label for="product_shipping_cost-error" class="error">
                                            {{ $errors->first('product_shipping_cost') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-winner" class="col-sm-2 control-label">{{ trans('product.form.winner') }}</label>
                                <div class="checkbox col-sm-6">
                                    <input {{ $errors->count() > 0 ? (old('product_winner') != '1' ? 'checked="checked"' : '') : ($product->winner == '1' ? 'checked="checked"' : '')  }} type="checkbox" id="product-winner" name="product_winner" value="1" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-stock" class="col-sm-2 control-label">{{ trans('product.form.stock') }}</label>
                                <div class="checkbox col-sm-6">
                                    <input {{ $errors->count() > 0 ? (old('product_stock') != '1' ? 'checked="checked"' : '') : ($product->stock == '1' ? 'checked="checked"' : '')  }} type="checkbox" id="product-stock" name="product_stock" value="1" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-parent-filters" class="col-sm-2 control-label">{{ trans('product.form.parent_filters') }}</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="product-parent-filters" name="product_parent_filters" value="{{ $errors->count() > 0 ? old('product_parent_filters') : $product->parent_filters }}">
                                    <p class="help-block">{{ trans('category.form.write_filters') }}</p>
                                    @if ($errors->has('product_parent_filters'))
                                        <label for="product_parent_filters-error" class="error">
                                            {{ $errors->first('product_parent_filters') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>{{ trans('product.form.seo') }}</legend>
                            <div class="form-group">
                                <label for="product-meta-title" class="col-sm-2 control-label">{{ trans('product.form.meta_title') }} <i class="required">*</i></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="product-meta-title" name="product_meta_title" value="{{ $errors->count() > 0 ? old('product_meta_title') : $product->meta_title }}">
                                    @if ($errors->has('product_meta_title'))
                                        <label for="product_meta_title-error" class="error">
                                            {{ $errors->first('product_meta_title') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-meta-description" class="col-sm-2 control-label">{{ trans('product.form.meta_description') }} <i class="required">*</i></label>
                                <div class="col-sm-6">
                                    <textarea rows="5" class="form-control" name="product_meta_description" id="product-meta-description" >{{ $errors->count() > 0 ? old('product_meta_description') : $product->meta_description }}</textarea>
                                    @if ($errors->has('product_meta_description'))
                                        <label for="product_meta_description-error" class="error">
                                            {{ $errors->first('product_meta_description') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-meta-noindex" class="col-sm-2 control-label">{{ trans('product.form.no_index') }}</label>
                                <div class="checkbox col-sm-6">
                                    <input {{ $errors->count() > 0 ? (old('product_meta_noindex') != '1' ? 'checked="checked"' : '') : ($product->meta_index == '1' ? 'checked="checked"' : '')  }} type="checkbox" id="product-meta-noindex" name="product_meta_noindex" value="1" />
                                </div>
                            </div>
                        </fieldset>
                        <div class="row">
                            <div class="col-sm-6 col-sm-offset-2">
                                <button type="submit" class="btn btn-primary">{{ trans('product.form.save_changes') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection