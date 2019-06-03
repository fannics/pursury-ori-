@extends('master')

@section('main_content')
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
            {{--<div class="panel panel-default">--}}
            {{--<div class="panel-body">--}}
            <div class="row login-form-container activation-form">
                <div class="col-sm-10 col-sm-offset-1">
                    <div class="login-form-container">
                        <h3 class="text-center">{{ trans('auth.activation.account_activation_title') }}</h3>
                        <p>{{ trans('auth.activation.account_activation_description') }}</p>
                        <button id="a" data-url="{{route('post_activation')}}" data-loading="{{ trans('auth.activation.requesting') }}" data-token="{{ $user->activate_token }}" type="button" class="btn btn-primary btn-lg">{{ trans('auth.activation.send_activation_mail') }}</button>
                        <hr />
                            <div class="text-center">
                                <a href="{{ prefixed_route('/auth/login') }}">{{ trans('auth.activation.register_go_to_login') }}</a>
                            </div>
                        <hr />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection