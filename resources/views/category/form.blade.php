@extends('admin')

@section('main_content')
    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <h2>{{ trans('category.form.edit_category') }}</h2>
                            <p>
                              {{ trans('category.form.summary') }}
                            </p>
                        </div>
                    </div>
                    @if ($errors->count() > 0)
                        <div class="alert alert-danger">
                          {{ trans('category.form.contains_errors') }}
                        </div>
                    @endif
                    <form class="form-horizontal" action="" method="post" id="category-form" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <fieldset>
                            <legend>{{ trans('category.form.category_data') }}</legend>
                            <div class="form-group">
                                <label for="category-title" class="col-sm-2 control-label">{{ trans('category.form.reference') }} </label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="category-reference" name="reference" value="{{ $errors->count() > 0 ? old('reference') : $category->reference }}">
                                    @if ($errors->has('reference'))
                                        <label id="category_reference-error" for="category-reference" class="error" >
                                            {{ $errors->first('reference') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="category-title" class="col-sm-2 control-label">{{ trans('category.form.title') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="category-title" name="title" value="{{ $errors->count() > 0 ? old('title') : $category->title }}">
                                    @if ($errors->has('title'))
                                        <label id="category_title-error" for="category-title" class="error" >
                                            {{ $errors->first('title') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="category-mapping" class="col-sm-2 control-label">{{ trans('category.form.mapping') }}</label>
                                <div class="col-sm-4">
                                    <input type="text" name="categories" id="category-mapping" class="form-control" value="{{ $errors->count() > 0 ? old('categories') : $category->categories }}" />
                                    @if ($errors->has('categories'))
                                        <label id="category_mapping-error" for="category-mapping" class="error" >
                                            {{ $errors->first('categories') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="category-parent" class="col-sm-2 control-label">{{ trans('category.form.parent_category') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <select name="parent_id" id="category-parent" class="form-control">
                                        <optgroup label="{{ trans('category.form.root_category') }}">
                                            <option {{ $errors->count() > 0 ? (!old('parent_id') ? 'selected="selected"' : '') : ($category->parent_id == null ? 'selected="selected"' : '') }} value="">{{ trans('category.form.root') }}</option>
                                        </optgroup>
                                        <optgroup label="{{ trans('category.form.other_categories') }}">
                                            @foreach($other_categories as $oc)
                                                <option {{ $errors->count() > 0 ? (old('parent_id') == $category->parent_id ? 'selected="selected"' : '') : ($category->parent_id == $oc->id ? 'selected="selected"' : '') }} value="{{ $oc->id }}">{{ $oc->title }}</option>
                                            @endforeach
                                        </optgroup>

                                    </select>
                                    @if ($errors->has('parent_id'))
                                        <label id="category_parent-error" for="category-parent" class="error" >
                                            {{ $errors->first('parent_id') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="category-url" class="col-sm-2 control-label">Url <i class="required">*</i></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="category-url" name="url_key" value="{{ $errors->count() > 0 ? old('url_key') : stripslashes($category->url_key) }}">
                                    @if ($errors->has('url_key'))
                                        <label id="category_url-error" for="category-url" class="error" >
                                            {{ $errors->first('url_key') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="category-description" class="col-sm-2 control-label">{{ trans('category.form.description') }}</label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" id="category-description" name="description">{{ $errors->count() > 0 ? old('description') : $category->description }}</textarea>
                                    @if ($errors->has('description'))
                                        <label id="category_description-error" for="category-description" class="error" >
                                            {{ $errors->first('description') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="category-short-description" class="col-sm-2 control-label">{{ trans('category.form.short_description') }}</label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" id="category-short-description" name="short_description">{{ $errors->count() > 0 ? old('short_description') : $category->short_description }}</textarea>
                                    @if ($errors->has('short_description'))
                                        <label id="category_short_description-error" for="category-short-description" class="error" >
                                            {{ $errors->first('short_description') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="category-visible" class="col-sm-2 control-label">{{ trans('category.form.visible') }}</label>
                                <div class="checkbox col-sm-6">
                                    <input {{ $errors->count() > 0 ? (old('is_visible') == '1' ? 'checked="checked"' : '') : ($category->is_visible == '1' ? 'checked="checked"' : '')  }} type="checkbox" id="category-visible" name="is_visible" value="1" />
                                    @if ($errors->has('is_visible'))
                                        <label id="category_visible-error" for="category-visible-description" class="error" >
                                            {{ $errors->first('is_visible') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="category-sorting" class="col-sm-2 control-label">{{ trans('category.form.sort_by_default') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <select class="form-control" id="category-sorting" name="default_sorting">
                                        @foreach([trans('category.form.popularity'), trans('category.form.name'), trans('category.form.price')] as $field)
                                            <option {{ $errors->count() > 0 ? (old('default_sorting') == $field ? 'selected="selected"' : '') : ($category->default_sorting == $field ? 'selected="selected"' : '') }} value="{{ $field }}">{{ $field }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('default_sorting'))
                                        <label id="category_sorting-error" for="category-sorting" class="error" >
                                            {{ $errors->first('default_sorting') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="category-filters" class="col-sm-2 control-label">{{ trans('category.form.filters') }}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="category-filters" name="filters" value="{{ $errors->count() > 0 ? old('filters') : $category->filters }}">
                                    <p class="help-block">{{ trans('category.form.write_filters') }}</p>
                                    @if ($errors->has('filters'))
                                        <label id="category_filters-error" for="category-filters" class="error" >
                                            {{ $errors->first('filters') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="img" class="col-sm-2 control-label">{{ trans('category.form.img') }}</label>

                                <div class="col-sm-6">
                                    <input type="file" class="form-control" id="img" name="img" >

                                    @if ($errors->has('filters'))
                                        <label id="img-error" for="img" class="error" >
                                            {{ $errors->first('img') }}
                                        </label>
                                    @endif
                                </div>
                                @if(file_exists(public_path('images/categories/img') . '/'.$category->img) && ($category->img != null) )
                                <div class="col-sm-4">
                                    <img style="width:100px; height:100px;" src="{{asset('images/categories/img').'/'.$category->img}}">
                                </div>
                                    @endif
                            </div>
                            <div class="form-group">
                                <label for="img_alt" class="col-sm-2 control-label">{{ trans('category.form.img_alt') }}</label>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="img_alt" name="img_alt" value="{{ $errors->count() > 0 ? old('img_alt') : $category->img_alt }}" >

                                    @if ($errors->has('img_alt'))
                                        <label id="img_alt-error" for="img_alt" class="error" >
                                            {{ $errors->first('img_alt') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="img_thumbnail" class="col-sm-2 control-label">{{ trans('category.form.img_thumbnail') }}</label>

                                <div class="col-sm-6">
                                    <input type="file" class="form-control" id="img_thumbnail" name="img_thumbnail" >

                                    @if ($errors->has('img_thumbnail'))
                                        <label id="img_thumbnail-error" for="img_thumbnail" class="error" >
                                            {{ $errors->first('img_thumbnail') }}
                                        </label>
                                    @endif
                                </div>

                                @if(file_exists(public_path('images/categories/img_thumbnail') . '/'.$category->img_thumbnail) && ($category->img_thumbnail != null))
                                    <div class="col-sm-4">
                                        <img style="width:100px; height:100px;" src="{{asset('images/categories/img_thumbnail').'/'.$category->img_thumbnail}}">
                                    </div>
                                @endif
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>SEO</legend>
                            <div class="form-group">
                                <label for="category-meta-title" class="col-sm-2 control-label">{{ trans('category.form.meta_title') }} <i class="required">*</i></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="category-meta-title" name="meta_title" value="{{ $errors->count() > 0 ? old('meta_title') : $category->meta_title }}">
                                    @if ($errors->has('meta_title'))
                                        <label id="category_meta_title-error" for="category-meta-title" class="error" >
                                            {{ $errors->first('meta_title') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="category-meta-description" class="col-sm-2 control-label">{{ trans('category.form.meta_description') }}</label>
                                <div class="col-sm-6">
                                    <textarea rows="5" class="form-control" name="meta_description" id="category-meta-description" >{{ $errors->count() > 0 ? old('meta_description') : $category->meta_description }}</textarea>
                                    @if ($errors->has('meta_description'))
                                        <label id="category_meta_description-error" for="category-meta-description" class="error" >
                                            {{ $errors->first('meta_description') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="category-meta-noindex" class="col-sm-2 control-label">{{ trans('category.form.no_index') }}</label>
                                <div class="checkbox col-sm-6">
                                    <input {{ $errors->count() > 0 ? (old('meta_no_index') == '1' ? 'checked="checked"' : '') : ($category->meta_no_index == '1' ? 'checked="checked"' : '')  }} type="checkbox" id="category-meta-noindex" name="meta_no_index" value="1" />
                                    @if ($errors->has('meta_no_index'))
                                        <label id="category_meta_noindex-error" for="category-meta-noindex" class="error" >
                                            {{ $errors->first('meta_no_index') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                        </fieldset>
                        <div class="row">
                            <div class="col-sm-6 col-sm-offset-2">
                                <button type="submit" class="btn btn-primary">{{ trans('category.form.save_changes') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascripts')
    <script type="text/javascript" src="{{ asset(settings('app.route_prefix').'/js/pages/admin_categories_form.js') }}"></script>
@endsection