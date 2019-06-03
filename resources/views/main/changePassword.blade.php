@extends('master')

@section('metadata')
    <meta NAME="robots" CONTENT="index, follow">
    <meta name="description" content="Cambiar contraseña">
@endsection

@section('title', settings('app.app_title').' - '.trans('main.changePassword.profile_change_password_title'))

@section('main_content')
    <div class="row profile-info">
        <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
            <h3 class="text-center">{{ trans('main.changePassword.profile_change_password_title') }}</h3>
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>{{ trans('main.changePassword.profile_edit_error_exclamation') }}</strong> {{ trans('main.changePassword.profile_edit_global_error') }}<br>
                </div>
            @endif
            <form action="{{ route('handle_change_password') }}" method="post" id="change-password-form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <input type="password" name="new_password" class="form-control input-lg" placeholder="{{ trans('main.changePassword.profile_new_password_placeholder') }}" />
                    @if ($errors->has('new_password'))
                        <label for="new_password" id="new_password-error" class="error">{{ $errors->first('new_password') }}</label>
                    @endif
                </div>
                <div class="form-group">
                    <input type="password" name="new_password_conf" class="form-control input-lg" placeholder="{{ trans('main.changePassword.profile_new_password_conf_placeholder') }}" />
                    @if ($errors->has('new_password_conf'))
                        <label for="new_password_conf" id="new_password_conf-error" class="error">{{ $errors->first('new_password_conf') }}</label>
                    @endif
                </div>
                <div class="form-group">
                    <button class="def-btn" type="submit" value=""><i class="fa fa-save"></i> {{ trans('main.changePassword.save_changes') }}</button>
                </div>
                <div class="form-group profile-buttons">
                    <a class="prim-bordered" href="{{ route('profile_edit') }}">{{ trans('main.changePassword.go_back') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection