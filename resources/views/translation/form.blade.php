@extends('admin')

@section('main_content')
    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">        
                    <div class="row">
                        <div class="col-xs-12">
                            <h2>{{ trans('translation.form.edit_translation') }}</h2>
                            <p>
                                {{ trans('translation.form.edit_translation_hint') }}
                            </p>
                        </div>
                    </div>
                    @if ($errors->count() > 0)
                        <div class="alert alert-danger">
                            {{ trans('translation.form.form_has_errors') }}
                        </div>
                    @endif
                    <form class="form-horizontal" action="" method="post" id="translation-form">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <fieldset>
                            <div class="form-group">
                                <label for="translation-group" class="col-sm-2 control-label">{{ trans('translation.form.group') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="translation-group" name="translation_group" value="{{ $errors->count() > 0 ? old('translation_group') : $translation->group }}" readonly>
                                    @if ($errors->has('translation_group'))
                                        <label id="translation_group-error" for="translation-group" class="error" >
                                            {{ $errors->first('translation_group') }}
                                        </label>
                                    @endif
                                </div>                           
                            </div>
                            <div class="form-group">
                                <label for="translation-item" class="col-sm-2 control-label">{{ trans('translation.form.item') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="translation-item" name="translation_item" value="{{ $errors->count() > 0 ? old('translation_item') : $translation->item }}" readonly>
                                    @if ($errors->has('translation_item'))
                                        <label id="translation_item-error" for="translation-item" class="error" >
                                            {{ $errors->first('translation_item') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="translation-text" class="col-sm-2 control-label">{{ trans('translation.form.text') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="translation-text" name="translation_text" value="{{ $errors->count() > 0 ? old('translation_text') : $translation->text }}">
                                    @if ($errors->has('translation_text'))
                                        <label id="translation_text-error" for="translation-text" class="error" >
                                            {{ $errors->first('translation_text') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                        </fieldset>

                        <div class="row">
                            <div class="col-sm-6 col-sm-offset-2">
                                <button type="submit" class="btn btn-primary">{{ trans('translation.form.save_changes') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascripts')
@endsection