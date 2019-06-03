@extends('master')

@section('metadata')
	<meta NAME="robots" CONTENT="index, follow">
	<meta name="description" content="{{ trans('auth.reset.recover_password_title') }}">
@endsection

@section('title', settings('app.app_title').' - '.trans('auth.reset.recover_password_title'))

@section('main_content')
	<div class="row">
		<div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
			{{--<div class="panel panel-default">--}}
			{{--<div class="panel-body">--}}
			<div class="row">
				<div class="col-sm-10 col-sm-offset-1">
					<div class="login-form-container">
						<h3 class="text-center">{{ trans('auth.reset.recover_password_title') }}</h3>
						<p class="form-info">{{ trans('auth.reset.recover_password_instructions') }}</p>
						@if (count($errors) > 0)
							<div class="alert alert-danger">
								<strong>{{ trans('auth.reset.profile_edit_error_exclamation') }}</strong> {{ trans('auth.reset.profile_edit_global_error') }}<br>
								<ul>
									@foreach ($errors->all() as $key=>$error)
										<li>{{ $error }}</li>
									@endforeach
								</ul>
							</div>
						@endif
						<form role="form" method="POST" action="{{ route('post_password_reset', ['token' => $token]) }}">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<input type="hidden" name="token" value="{{ $token }}">

							<div class="form-group">
								<input type="email" class="form-control input-lg" name="email" value="{{ old('email') }}"  placeholder="{{ trans('auth.reset.register_email_placeholder') }}"/>
							</div>

							<div class="form-group">
								<input type="password" class="form-control input-lg" name="password" placeholder="{{ trans('auth.reset.register_password_placeholder') }}"/>
							</div>

							<div class="form-group">
								<input type="password" class="form-control input-lg" name="password_confirmation" placeholder="{{ trans('auth.reset.register_password_conf_placeholder') }}">
							</div>

							<div class="form-group">
								<button type="submit" class="btn btn-primary btn-lg" style="margin-right: 15px;">
									{{ trans('auth.reset.do_recover_password') }}
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
