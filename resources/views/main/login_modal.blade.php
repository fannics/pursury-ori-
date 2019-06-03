<div class="modal fade" id="login-required-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ trans('main.login_modal.login_modal_title') }}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <ul class="nav nav-tabs custom-tabs" role="tablist">
                            <li role="presentation" class="active"><a class="text-center" href="#login" aria-controls="home" role="tab" data-toggle="tab">{{ trans('main.login_modal.login_modal_login_tab') }}</a></li>
                            <li role="presentation"><a class="text-center" href="#register" aria-controls="profile" role="tab" data-toggle="tab">{{ trans('main.login_modal.login_modal_register_tab') }}</a></li>
                        </ul>

                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="login">
                                <div class="row">
                                    <div class="col-sm-10 col-sm-offset-1">
                                        <div class="login-form-container">
                                            <div style="margin-top: 10px;" class="text-center">{{ trans('main.login_modal.login_modal_fill_fields_for') }} <b>{{ trans('main.login_modal.login_modal_login') }}</b></div>
                                            @if (count($errors) > 0)
                                                <div class="alert alert-danger">
                                                    <strong>{{ trans('main.login_modal.profile_edit_error_exclamation') }}</strong> {{ trans('main.login_modal.profile_edit_global_error') }}<br>
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
                                                    <input type="email" class="form-control input-lg" name="email" value="{{ old('email') }}" placeholder="{{ trans('main.login_modal.register_email_placeholder') }}">
                                                </div>

                                                <div class="form-group">
                                                    <input type="password" class="form-control input-lg" name="password" placeholder="{{ trans('main.login_modal.register_password_placeholder') }}">
                                                </div>
                                                <div class="form-group">
                                                    <div class="checkbox">
                                                        <input type="checkbox" name="remember"> {{ trans('main.login_modal.remember_password') }}
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary btn-lg" style="margin-right: 15px;">
                                                        {{ trans('main.login_modal.login_do_login') }}
                                                    </button>
                                                </div>
                                                <div class="form-group">
                                                    <a href="/password/email">{{ trans('main.login_modal.login_forgot_password') }}</a>
                                                </div>
                                                <hr/>
                                                <div class="form-group">
                                                    <p class="text-center">{{ trans('main.login_modal.login_register_message') }} <a href="{{ prefixed_route('/auth/register') }}">{{ trans('main.login_modal.login_register_here_link_text') }}</a></p>
                                                </div>
                                                <hr/>
                                                <div class="form-group">
                                                    <p class="text-center">{{ trans('main.login_modal.register_social_choice') }}</p>
                                                    <ul class="social-login-list">
                                                        <li>
                                                            <a class="facebook" href="{{ route('social_login_redirect', ['provider' => 'facebook']) }}"><i class="fa fa-facebook fa-lg"></i> <span>{{ trans('main.login_modal.enter_using_facebook') }}</span></a>
                                                        </li>
                                                        <li>
                                                            <a class="google-plus" href="{{ route('social_login_redirect', ['provider' => 'google']) }}"><i class="fa fa-lg fa-google-plus"></i> <span>{{ trans('main.login_modal.enter_using_google') }}</span></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="register">
                                <div class="row">
                                    <div class="col-sm-10 col-sm-offset-1">
                                        <div class="login-form-container">
                                            <div class="text-center" style="margin-top: 10px;">{{ trans('main.login_modal.login_modal_fill_fields_for') }} <b>{{ trans('main.login_modal.login_modal_register') }}</b></div>
                                            <form role="form" method="POST" action="{{ prefixed_route('auth/register') }}" id="register-form" novalidate>
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <div class="form-group">
                                                    <input type="text" class="form-control input-lg" name="name" placeholder="{{ trans('main.login_modal.register_fullname_placeholder') }}" />
                                                </div>

                                                <div class="form-group">
                                                    <input type="email" class="form-control input-lg" name="email" data-remote-checker="{{ prefixed_route('auth/check-email') }}" placeholder="{{ trans('main.login_modal.register_email_placeholder') }}" />
                                                </div>
                                                <div class="form-group">
                                                    <input type="password" class="form-control input-lg" name="password" placeholder="{{ trans('main.login_modal.register_password_placeholder') }}" />
                                                </div>
                                                <div class="form-group">
                                                    <input type="password" class="form-control input-lg" name="password_confirmation" placeholder="{{ trans('main.login_modal.register_password_conf_placeholder') }}" />
                                                </div>
                                                <div class="form-group">
                                                    <ul class="list-inline">
                                                        <li><input type="radio" name="gender" id="gender" value="male"> {{ trans('main.login_modal.profile_gender_male') }}</li>
                                                        <li><input type="radio" name="gender" id="gender" value="female"> {{ trans('main.login_modal.profile_gender_female') }}</li>
                                                    </ul>
                                                </div>
                                                <div class="form-group">
                                                    <div class="checkbox">
                                                        <input type="checkbox" name="newsletter" value="1"> {{ trans('main.login_modal.register_newsletter_check') }}
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary btn-lg" style="margin-right: 15px;">
                                                        {{ trans('main.login_modal.register_do_register') }}
                                                    </button>
                                                </div>
                                                <hr/>
                                                <div class="form-group">
                                                    <p class="text-center">{{ trans('main.login_modal.register_social_choice') }}</p>
                                                    <ul class="social-login-list">
                                                        <li>
                                                            <a class="facebook" href="{{ route('social_login_redirect', ['provider' => 'facebook']) }}"><i class="fa fa-facebook fa-lg"></i> <span>{{ trans('main.login_modal.enter_using_facebook') }}</span></a>
                                                        </li>
                                                        <li>
                                                            <a class="google-plus" href="{{ route('social_login_redirect', ['provider' => 'google']) }}"><i class="fa fa-lg fa-google-plus"></i> <span>{{ trans('main.login_modal.enter_using_google') }}</span></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
