@extends('master')

@section('metadata')
    <meta NAME="robots" CONTENT="index, follow">
    <meta name="description" content="Inicia sesión en nuestro sitio para obtener mejores posibilidades">
@endsection

@section('title', settings('app.app_title').' - '.trans('main.profile_edit.edit_profile_title'))

@section('main_content')
<div class="row profile-info">
    <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
        <form action="{{ route('handle_profile_edit') }}" method="post" novalidate id="profile-form">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="text-center">
                <div class="profile-picture">
                    @if($user->profile_photo_url)
                        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                    @else
                        <img src="/images/default-profile.png" alt="{{ $user->name }}">
                    @endif
                </div>
                <h3>{{ $user->name }}</h3>
            </div>
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>{{ trans('main.profile_edit.profile_edit_error_exclamation') }}</strong> {{ trans('main.profile_edit.profile_edit_global_error') }}<br>
                </div>
            @endif
            <div class="form-group">
                <input type="text" name="username" class="form-control input-lg" placeholder="{{ trans('main.profile_edit.profile_edit_fullname_placeholder') }}" value="{{ old('username', $user->name) }}">
                @if ($errors->has('username'))
                    <label for="username" id="username-error" class="error">{{ trans('main.profile_edit.mandatory_field') }}</label>
                @endif
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-6">
                        <input type="text" name="city" id="city" class="form-control input-lg" placeholder="{{ trans('main.profile_edit.profile_edit_city_placeholder') }}" value="{{ old('city', $user->city) }}"/>
                    </div>
                    <div class="col-sm-6">
                        <input type="text" name="country" id="country" class="form-control input-lg" placeholder="{{ trans('main.profile_edit.profile_edit_country_placeholder') }}" value="{{ old('country', $user->country) }}"/>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div>
                    <label class="radio-inline">
                        <input type="radio" name="gender" id="gender" value="male" {{ old('gender', $user->gender) == 'male' ? 'checked="checked"' : '' }}> {{ trans('main.profile_edit.profile_gender_male') }}
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="gender" id="gender" value="female" {{ old('gender', $user->gender) == 'female' ? 'checked="checked"' : '' }}> {{ trans('main.profile_edit.profile_gender_female') }}
                    </label>
                </div>
                @if ($errors->has('gender'))
                    <label for="gender" id="gender-error" class="error">{{ trans('main.profile_edit.mandatory_field') }}</label>
                @endif
            </div>
            <div class="form-group">
                <textarea name="brief_description" id="breif-description" rows="5" class="form-control input-lg" placeholder="{{ trans('main.profile_edit.profile_edit_briefing_description') }}">{{ old('brief_description', $user->brief_description) }}</textarea>
            </div>
            <div class="form-group">
                <input type="url" name="url" class="form-control input-lg" placeholder="{{ trans('main.profile_edit.profile_edit_blog_or_web') }}" value="{{ old('url', $user->url) }}">
                @if ($errors->has('url'))
                    <label for="url" id="url-error" class="error">{{ trans('main.profile_edit.invalid_url_with_sample') }}</label>
                @endif
            </div>
            <div class="form-group">
                <button class="def-btn" type="submit" value=""><i class="fa fa-save"></i> {{ trans('main.profile_edit.save_changes') }}</button>
            </div>
            <div class="row profile-buttons">
                <div class="col-sm-12" style="margin-bottom: 10px;">
                    <a class="prim-bordered" href="{{ route('change_password') }}">
                        <i class="fa fa-asterisk"></i> {{ trans('main.profile_edit.change_password') }}
                    </a>
                </div>
                <div class="col-sm-12" style="margin-bottom: 10px;">
                    <a class="prim-bordered" href="{{ route('change_email') }}">
                        <i class="fa fa-lock"></i> {{ trans('main.profile_edit.change_email') }}
                    </a>
                </div>
                <div class="col-sm-12" style="margin-bottom: 10px;">
                    <a class="prim-bordered" href="{{ route('change_notifications') }}">
                        <i class="fa fa-envelope"></i> {{ trans('main.profile_edit.change_notifications') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('javascripts')
    <script type="text/javascript">
        $(function(){
            $('#profile-form').validate({
                rules: {
                    username: 'required',
                    gender: 'required',
                    url: 'url'
                },
                messages: {
                    username: 'Este campo es obligatorio',
                    gender: 'Este campo es obligatorio',
                    url: 'La url del blog o sitio web es inválida, debe seguir el formato http://dominio.com'
                }
            });
        });
    </script>
@endsection


