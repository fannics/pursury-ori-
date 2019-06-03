@extends('master')

@section('metadata')
	<meta NAME="robots" CONTENT="index, follow">
	<meta name="description" content="{{ trans('auth.password.meta_description') }}">         
@endsection

@section('title', settings('app.app_title').' - '.trans('auth.password.password_title'))

@section('main_content')

	<div class="row">
		<div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
			<div class="row">
				<div class="col-sm-10 col-sm-offset-1">
					<div class="login-form-container">
						<h3 class="text-center">{{ trans('auth.password.password_title') }}</h3>
						<p class="form-info">{{ trans('auth.password.summary') }}</p>
						@if (count($errors) > 0)
							<div class="alert alert-danger">
								<strong>{{ trans('auth.password.Oops') }}</strong> {{ trans('auth.password.global_error') }}<br><br>
								<ul>
									@foreach ($errors->all() as $error)
										<li>{{ $error }}</li>
									@endforeach
								</ul>
							</div>
						@endif
						<form role="form" method="POST" action="{{ prefixed_route('/password/email') }}" id="password-form">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<div class="form-group">
								<input type="email" class="form-control input-lg" name="email" value="{{ old('email') }}"  placeholder="{{ trans('auth.password.email_address') }}"/>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-primary btn-lg" style="margin-right: 15px;">
                  {{ trans('auth.password.send') }}
								</button>
							</div>
							<hr>
							<div class="text-center">
								<a href="{{ prefixed_route('/auth/login') }}">{{ trans('auth.password.back_login') }}</a>
							</div>
							<hr>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('javascripts')
	<script type="text/javascript" src="{{ asset(settings('app.route_prefix').'/dist/js/password.js') }}"></script>
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