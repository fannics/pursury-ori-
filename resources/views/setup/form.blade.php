@extends('admin')

@section('main_content')
    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <h2>{{ trans('setup.form.edit_setup') }}</h2>
                            <p>
                                {{ trans('setup.form.edit_setup_hint') }}
                            </p>
                        </div>
                    </div>
                    @if ($errors->count() > 0)
                        <div class="alert alert-danger">
                            {{ trans('setup.form.form_has_errors') }}
                        </div>
                    @endif
                    <form class="form-horizontal" action="" method="post" id="setup-form">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <fieldset>
                            <div class="form-group">
                                <label for="setup-country" class="col-sm-2 control-label">{{ trans('setup.form.country') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="setup-country" name="setup_country" value="{{ $errors->count() > 0 ? old('setup_country') : $setup->country }}">
                                    @if ($errors->has('setup_country'))
                                        <label id="setup_country-error" for="setup-country" class="error" >
                                            {{ $errors->first('setup_country') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="setup-country-abre" class="col-sm-2 control-label">{{ trans('setup.form.country_abbreviation') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control shortTextField" id="setup-country-abre" name="setup_country_abre" value="{{ $errors->count() > 0 ? old('setup_country_abre') : $setup->country_abre }}" maxlength="2">
                                    <small><b>{{ trans('setup.form.hint') }}:</b> {{ trans('setup.form.you_should_use') }} <a href="https://en.wikipedia.org/wiki/ISO_3166-1#Current_codes" target="_blank">{{ trans('setup.form.iso_3166_code') }}</a></small>
                                    @if ($errors->has('setup_country_abre'))
                                        <label id="setup_country_abre-error" for="setup-country-abre" class="error" >
                                            {{ $errors->first('setup_country_abre') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="setup-language" class="col-sm-2 control-label">{{ trans('setup.form.language') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="setup-language" name="setup_language" value="{{ $errors->count() > 0 ? old('setup_language') : $setup->language }}">
                                    @if ($errors->has('setup_language'))
                                        <label id="setup_language-error" for="setup-language" class="error" >
                                            {{ $errors->first('setup_language') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="setup-default-language" class="col-sm-2 control-label">{{ trans('setup.form.default_language') }} </label>
                                <div class="col-sm-4">
                                    @if ($setup->default_language==0)
                                      <input type="checkbox" class="form-control" id="setup-default-language" name="setup_default_language" value="1">
                                    @else
                                      <input type="checkbox" class="form-control" id="setup-default-language" name="setup_default_language" value="1" checked>
                                    @endif
                                    @if ($errors->has('setup_default_language'))
                                        <label id="setup_default_language-error" for="setup-default-language" class="error" >
                                            {{ $errors->first('setup_default_language') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="setup-language-abre" class="col-sm-2 control-label">{{ trans('setup.form.language_abbreviation') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control shortTextField" id="setup-language-abre" name="setup_language_abre" value="{{ $errors->count() > 0 ? old('setup_language_abre') : $setup->language_abre }}" maxlength="2">
                                    <small><b>{{ trans('setup.form.hint') }}:</b> {{ trans('setup.form.you_should_use') }} <a href="https://en.wikipedia.org/wiki/ISO_639-1" target="_blank"> {{ trans('setup.form.iso_639_code') }}</a></small>
                                    @if ($errors->has('setup_language_abre'))
                                        <label id="setup_language_abre-error" for="setup-language-abre" class="error" >
                                            {{ $errors->first('setup_language_abre') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="setup-currency" class="col-sm-2 control-label">{{ trans('setup.form.currency') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control shortTextField2" id="setup-currency" name="setup_currency" value="{{ $errors->count() > 0 ? old('setup_currency') : $setup->currency }}"  maxlength=3>
                                    <small><b>{{ trans('setup.form.hint') }}:</b> {{ trans('setup.form.you_should_use') }} <a href="https://en.wikipedia.org/wiki/ISO_4217#Active_codes" target="_blank">{{ trans('setup.form.iso_4217_code') }}</a></small>
                                    @if ($errors->has('setup_currency'))
                                        <label id="setup_currency-error" for="setup-currency" class="error" >
                                            {{ $errors->first('setup_currency') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="setup-currency-symbol" class="col-sm-2 control-label">{{ trans('setup.form.currency_symbol') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control shortTextField" id="setup-currency-symbol" name="setup_currency_symbol" value="{{ $errors->count() > 0 ? old('setup_currency_symbol') : $setup->currency_symbol }}">
                                    @if ($errors->has('setup_currency_symbol'))
                                        <label id="setup_currency_symbol-error" for="setup-currency-symbol" class="error" >
                                            {{ $errors->first('setup_currency_symbol') }}
                                        </label>
                                    @endif
                                </div>
                            </div>                                                           
                            <div class="form-group">
                                <label for="setup-before-after" class="col-sm-2 control-label">{{ trans('setup.form.symbol_position') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    @if ($setup->before_after==0)
                                      <input type="radio" class="form-control" id="setup-before-after" name="setup_before_after" value="0" checked /> {{ trans('setup.form.before') }}  
                                      <input type="radio" class="form-control" id="setup-before-after" name="setup_before_after" value="1" /> {{ trans('setup.form.after') }}
                                    @else
                                      <input type="radio" class="form-control" id="setup-before-after" name="setup_before_after" value="0" /> {{ trans('setup.form.before') }}  
                                      <input type="radio" class="form-control" id="setup-before-after" name="setup_before_after" value="1" checked /> {{ trans('setup.form.after') }}                                      
                                    @endif                                      
                                    @if ($errors->has('setup_before_after'))
                                        <label id="setup_before_after-error" for="setup-before-after" class="error" >
                                            {{ $errors->first('setup_before_after') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="setup-currency-decimal" class="col-sm-2 control-label">{{ trans('setup.form.currency_decimal') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    {!! 
                                      Form::select('setup_currency_decimal', 
                                            [
                                              'comma' => trans('setup.create.comma'),
                                              'point' => trans('setup.create.point'),
                                              'arabic' => trans('setup.create.arabic')
                                            ],
                                            $setup->currency_decimal,
                                            array('id' => 'setup-currency-decimal', 'class' => 'form-control')
                                      ) 
                                    !!}
                                    @if ($errors->has('setup_currency_decimal'))
                                        <label id="setup_currency_decimal-error" for="setup-currency-decimal" class="error" >
                                            {{ $errors->first('setup_currency_decimal') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="setup-default-setup" class="col-sm-2 control-label">{{ trans('setup.form.default_setup') }} </label>
                                <div class="col-sm-4">
                                    @if ($setup->default_setup==0)
                                      <input type="checkbox" class="form-control" id="setup-default-setup" name="setup_default_setup" value="1">
                                    @else
                                      <input type="checkbox" class="form-control" id="setup-default-setup" name="setup_default_setup" value="1" checked>
                                    @endif
                                    @if ($errors->has('setup_default_setup'))
                                        <label id="setup_default_setup-error" for="setup-default-setup" class="error" >
                                            {{ $errors->first('setup_default_setup') }}
                                        </label>
                                    @endif
                                </div>
                            </div>
                        </fieldset>

                        <div class="row">
                            <div class="col-sm-6 col-sm-offset-2">
                                <button type="submit" class="btn btn-primary">{{ trans('setup.form.save_changes') }}</button>
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