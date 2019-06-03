@extends('master')

@section('metadata')
    <meta NAME="robots" CONTENT="index, follow">
    <meta name="description" content="Cambie su dirección de correo electrónico">
@endsection

@section('title', settings('app.app_title').' - '.trans('main.changeEmail.profile_change_email_title'))

@section('main_content')
    <div class="row profile-info">
        <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
            <h3 class="text-center">{{ trans('main.changeEmail.profile_change_email_title') }}</h3>
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>{{ trans('main.changeEmail.profile_edit_error_exclamation') }}</strong> {{ trans('main.changeEmail.profile_edit_global_error') }}<br>
                </div>
            @endif
            <form action="{{ route('handle_change_email') }}" method="post" id="change-email" novalidate>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <input type="email" name="new_email" class="form-control input-lg" placeholder="{{ trans('main.changeEmail.change_email_placeholder') }}" />
                    @if ($errors->has('new_email'))
                        <label for="new_email" id="new_email-error" class="error">{{ $errors->first('new_email') }}</label>
                    @endif
                </div>
                <div class="form-group">
                    <input type="email" name="new_email_conf" class="form-control input-lg" placeholder="{{ trans('main.changeEmail.repeat_email_confirmation') }}" />
                    @if ($errors->has('new_email_conf'))
                        <label for="new_email_conf" id="new_email_conf-error" class="error">{{ $errors->first('new_email_conf') }}</label>
                    @endif
                </div>
                <div class="form-group">
                    <button class="def-btn" type="submit" value=""><i class="fa fa-save"></i> {{ trans('main.changeEmail.save_changes') }}</button>
                </div>
                <div class="form-group profile-buttons">
                    <a class="prim-bordered" href="{{ route('profile_edit') }}">{{ trans('main.changeEmail.go_back') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection

