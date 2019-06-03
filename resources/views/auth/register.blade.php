@extends('master')

@section('metadata')
	<meta NAME="robots" CONTENT="index, follow">
	<meta name="description" content="{{ trans('auth.register.register_seo_description') }}">

	<meta property="og:title" content="{{ settings('app.app_title').' - '.trans('auth.register.register_title') }}" />
	<meta property="og:type" content="website" />
	<meta property="og:image" content="{{ asset(settings('app.route_prefix').'/images/watermarksincom_resized.png') }}" />
	<meta property="og:description" content="Registro de usuarios" />
	<meta property="og:site_name" content="{{ settings('app.app_title') }}" />

@endsection

@section('title', settings('app.app_title').' - '.trans('auth.register.register_title'))

@section('main_content')

	<div class="row">
		<div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
			<div class="row">
				<div class="col-sm-10 col-sm-offset-1">
					<div class="login-form-container">
						<h3 class="text-center">{{ trans('auth.register.register_title') }}</h3>
						@if (count($errors) > 0)
							<div class="alert alert-danger">
								<strong>{{ trans('auth.register.profile_edit_error_exclamation') }}</strong> {{ trans('auth.register.profile_edit_global_error') }}<br>
							</div>
						@endif
						<form role="form" method="POST" action="{{ prefixed_route('/auth/register') }}" id="register-form" novalidate>
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<div class="form-group">
								<input type="text" class="form-control input-lg" name="name" value="{{ old('name') }}" placeholder="{{ trans('auth.register.register_fullname_placeholder') }}" />
								@if ($errors->has('name'))
									<label for="name" id="name-error" class="error">{{ $errors->first('name') }}</label>
								@endif
							</div>

							<div class="form-group">
								<input type="email" class="form-control input-lg" name="email" data-remote-checker="{{ prefixed_route('/auth/check-email') }}" value="{{ old('email') }}" placeholder="{{ trans('auth.register.register_email_placeholder') }}" />
								@if ($errors->has('email'))
									<label for="email" id="email-error" class="error">{{ $errors->first('email') }}</label>
								@endif
							</div>
							<div class="form-group">
								<input type="password" class="form-control input-lg" name="password" placeholder="{{ trans('auth.register.register_password_placeholder') }}" />
								@if ($errors->has('password'))
									<label for="password" id="password-error" class="error">{{ $errors->first('password') }}</label>
								@endif
							</div>
							<div class="form-group">
								<input type="password" class="form-control input-lg" name="password_confirmation" placeholder="{{ trans('auth.register.register_password_conf_placeholder') }}" />
								@if ($errors->has('password_confirmation'))
									<label for="password_confirmation" id="password_confirmation-error" class="error">{{ $errors->first('password_confirmation') }}</label>
								@endif
							</div>
							<div class="form-group">
								<ul class="list-inline">
									<li><input type="radio" name="gender" id="gender" value="male"> {{ trans('auth.register.profile_gender_male') }}</li>
									<li><input type="radio" name="gender" id="gender" value="female"> {{ trans('auth.register.profile_gender_female') }}</li>
								</ul>
								@if ($errors->has('gender'))
									<label for="gender" id="gender-error" class="error">{{ $errors->first('gender') }}</label>
								@endif
							</div>
							<div class="form-group">
								<div class="checkbox">
									<input type="checkbox" name="newsletter" value="1"> {{ trans('auth.register.register_newsletter_check') }}
								</div>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-primary btn-lg" style="margin-right: 15px;">
									{{ trans('auth.register.register_do_register') }}
								</button>
							</div>
							<hr />
							<div class="text-center">
								<a href="{{ prefixed_route('/auth/login') }}">{{ trans('auth.register.register_go_to_login') }}</a>
							</div>
							<hr/>
							<div class="form-group">
								<p class="text-center">{{ trans('auth.register.register_social_choice') }}</p>
								<ul class="social-login-list">
									<li>
										<a class="facebook" href="{{ route('social_login_redirect', ['provider' => 'facebook']) }}"><i class="fa fa-facebook fa-lg"></i> <span>{{ trans('auth.register.enter_using_facebook') }}</span></a>
									</li>
									<li>
										<a class="google-plus" href="{{ route('social_login_redirect', ['provider' => 'google']) }}"><i class="fa fa-lg fa-google-plus"></i> <span>{{ trans('auth.register.enter_using_google') }}</span></a>
									</li>
								</ul>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection