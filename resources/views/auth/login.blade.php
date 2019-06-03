@extends('master')

@section('metadata')
	<meta NAME="robots" CONTENT="index, follow">
	<meta name="description" content="{{ trans('auth.login.meta_description') }}">

	<meta property="og:title" content="{{ settings('app.app_title').' - '.trans('auth.login.login_title') }}" />
	<meta property="og:type" content="website" />
	<meta property="og:image" content="{{ asset(settings('app.route_prefix').'/images/watermarksincom_resized.png') }}" />
	<meta property="og:description" content="{{ trans('auth.login.login_title') }}" />
	<meta property="og:site_name" content="{{ settings('app.app_title') }}" />

@endsection

@section('title', settings('app.app_title').' - '.trans('auth.login.login_title'))

@section('main_content')
	<div class="row">
		<div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
			{{--<div class="panel panel-default">--}}
				{{--<div class="panel-body">--}}
					<div class="row">
						<div class="col-sm-10 col-sm-offset-1">
							<div class="login-form-container">
								<h3 class="text-center">{{ trans('auth.login.login_title') }}</h3>
								@if (count($errors) > 0)
									<div class="alert alert-danger">
										<strong>{{ trans('auth.login.profile_edit_error_exclamation') }}</strong> {{ trans('auth.login.profile_edit_global_error') }}<br>
										<ul>
											@foreach ($errors->all() as $error)
												<li>{{ $error }}</li>
											@endforeach
										</ul>
									</div>
								@endif
								<form role="form" method="POST" action="{{ prefixed_route('/auth/login') }}" id="login-form">
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
									<div class="form-group">
										<input type="email" class="form-control input-lg" name="email" value="{{ old('email') ? old('email') : ($email ? $email : '') }}" placeholder="{{ trans('auth.login.register_email_placeholder') }}">
									</div>

									<div class="form-group">
										<input type="password" class="form-control input-lg" name="password" placeholder="{{ trans('auth.login.register_password_placeholder') }}">
									</div>
									<div class="form-group">
										<div class="checkbox">
											<input type="checkbox" name="remember"> {{ trans('auth.login.remember_password') }}
										</div>
									</div>
									<div class="form-group">
										<button type="submit" class="btn btn-primary btn-lg" style="margin-right: 15px;">
											{{ trans('auth.login.login_do_login') }}
										</button>
									</div>
									<div class="form-group">                                                   
										<a href="{{ prefixed_route('/password/email') }}">{{ trans('auth.login.login_forgot_password') }}</a>
									</div>
									<hr/>
									<div class="form-group">
										<p class="text-center">{{ trans('auth.login.login_register_message') }} <a href="{{ prefixed_route('/auth/register') }}">{{ trans('auth.login.login_register_here_link_text') }}</a></p>
									</div>
									<hr/>
									<div class="form-group">
										<p class="text-center">{{ trans('auth.login.register_social_choice') }}</p>
										<ul class="social-login-list">
											<li>
												<a class="facebook" href="{{ route('social_login_redirect', ['provider' => 'facebook']) }}"><i class="fa fa-facebook fa-lg"></i> <span>{{ trans('auth.login.enter_using_facebook') }}</span></a>
											</li>
											<li>
												<a class="google-plus" href="{{ route('social_login_redirect', ['provider' => 'google']) }}"><i class="fa fa-lg fa-google-plus"></i> <span>{{ trans('auth.login.enter_using_google') }}</span></a>
											</li>
										</ul>
									</div>
								</form>
							</div>
						</div>
					</div>

				{{--</div>--}}                          
			{{--</div>--}}
		</div>
	</div>
@endsection

@section('javascripts')
	<script type="text/javascript" src="{{ asset(settings('app.route_prefix').'dist/js/login.js') }}"></script>
  <script>
    $.extend(
      $.validator.messages, {
        required: "{{ trans('validation.required') }}",
        remote: "{{ trans('validation.remote') }}",
        email: "{{ trans('validation.email') }}",
        url: "{{ trans('validation.url') }}",
        date: "{{ trans('validation.date') }}",
        dateISO: "{{ trans('validation.dateISO') }}",
        number: "{{ trans('validation.number') }}",
        digits: "{{ trans('validation.digits') }}",
        creditcard: "{{ trans('validation.creditcard') }}",
        equalTo: "{{ trans('validation.equalTo') }}",
        extension: "{{ trans('validation.extension') }}",
        maxlength: $.validator.format("{{ trans('validation.maxlength') }}"),
        minlength: $.validator.format("{{ trans('validation.minlength') }}"),
        rangelength: $.validator.format("{{ trans('validation.rangelength') }}"),
        range: $.validator.format("{{ trans('validation.range') }}"),
        max: $.validator.format("{{ trans('validation.max') }}"),
        min: $.validator.format("{{ trans('validation.min') }}"),
        nifES: "{{ trans('validation.nifES') }}",
        nieES: "{{ trans('validation.nieES') }}",
        cifES: "{{ trans('validation.cifES') }}"
      }
    );  
  </script>
@endsection
  
