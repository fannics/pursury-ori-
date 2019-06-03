@extends('admin')

@section('main_content')
    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <h2>{{ trans('user.form.admin_users') }}</h2>
                            <p>
                                {{ trans('user.form.admin_users_hint') }}
                            </p>
                        </div>
                    </div>
                    <form class="form-horizontal" action="{{ route('admin_users_edit_form_process', ['id' => $id]) }}" method="post" id="product-form">
                        <fieldset>
                            <legend>{{ trans('user.form.user_data') }}</legend>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <input type="hidden" name="id" value="{{ $id }}">
                            <div class="form-group">
                                <label for="user-name" class="col-sm-2 control-label">{{ trans('user.form.full_name') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="user-name" name="user_name" value="{{ $errors->count() > 0 ? old('user_name') : $user->name }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="user-email" class="col-sm-2 control-label">{{ trans('user.form.email') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="user-email" name="user_email" value="{{ $errors->count() > 0 ? old('user_email') : $user->email }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="product-meta-noindex" class="col-sm-2 control-label">{{ trans('user.form.genre') }} <i class="required">*</i></label>
                                <div class="checkbox col-sm-6">
                                    <input {{ $errors->count() > 0 ? (old('user_gender') == 'male' ? 'checked="checked"' : '') : ($user->gender == 'male' ? 'checked="checked"' : '')  }} type="radio" id="user-gender" name="user_gender" value="male" /> {{ trans('user.form.man') }}
                                    <input {{ $errors->count() > 0 ? (old('user_gender') == 'female' ? 'checked="checked"' : '') : ($user->gender == 'female' ? 'checked="checked"' : '')  }} type="radio" id="user-gender" name="user_gender" value="female" /> {{ trans('user.form.woman') }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="user-description" class="col-sm-2 control-label">{{ trans('user.form.brief_description') }} </label>
                                <div class="col-sm-4">
                                    <textarea class="form-control" id="user-description" name="user_description" rows="5">{{ $errors->count() > 0 ? old('user_description') : $user->brief_description }}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="user-url" class="col-sm-2 control-label">{{ trans('user.form.blog_personal_website') }} <i class="required">*</i></label>
                                <div class="col-sm-4">
                                    <input type="url" class="form-control" id="user-url" name="user_url" value="{{ $errors->count() > 0 ? old('user_url') : $user->url }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="user-city" class="col-sm-2 control-label">{{ trans('user.form.city') }} </label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="user-city" name="user_city" value="{{ $errors->count() > 0 ? old('user_city') : $user->city }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="user-country" class="col-sm-2 control-label">{{ trans('user.form.country') }} </label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="user-country" name="user_country" value="{{ $errors->count() > 0 ? old('user_country') : $user->country }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="user-active" class="col-sm-2 control-label">{{ trans('user.form.active') }}</label>
                                <div class="checkbox col-sm-6">
                                    <input {{ $errors->count() > 0 ? (old('user_active') == '1' ? 'checked="checked"' : '') : ($user->active == '1' ? 'checked="checked"' : '')  }} type="checkbox" id="user-active" name="user_active" value="1" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="user-newsletter" class="col-sm-2 control-label">{{ trans('user.form.newsletter') }}</label>
                                <div class="checkbox col-sm-6">
                                    <input {{ $errors->count() > 0 ? (old('user_newsletter') == '1' ? 'checked="checked"' : '') : ($user->newsletter == '1' ? 'checked="checked"' : '')  }} type="checkbox" id="user-newsletter" name="user_newsletter" value="1" />
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>{{ trans('user.form.set_password') }}</legend>
                            <div class="form-group">
                                <label for="user-password" class="col-sm-2 control-label">{{ trans('user.form.password') }}</label>
                                <div class="col-sm-4">
                                    <input type="password" class="form-control" id="user-password" name="user_password" value="" />
                                    <p class="help-block">{{ trans('user.form.want_change_password') }} </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="user-password-confirm" class="col-sm-2 control-label">{{ trans('user.form.confirm_password') }}</label>
                                <div class="col-sm-4">
                                    <input type="password" class="form-control" id="user-password-confirm" name="user_password_confirm" value="" />
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>Rol</legend>
                            <div class="form-group">
                                <label for="product-meta-noindex" class="col-sm-2 control-label">{{ trans('user.form.user_role') }} </label>
                                <div class="checkbox col-sm-6">
                                    <input {{ $errors->count() > 0 ? (old('user_role') == 'ROLE_FRONT_USER' ? 'checked="checked"' : '') : ($user->role == 'ROLE_FRONT_USER' ? 'checked="checked"' : '')  }} type="radio" id="user-role" name="user_role" value="ROLE_FRONT_USER" /> {{ trans('user.form.user') }}
                                    <input {{ $errors->count() > 0 ? (old('user_role') == 'ROLE_ADMIN' ? 'checked="checked"' : '') : ($user->role == 'ROLE_ADMIN' ? 'checked="checked"' : '')  }} type="radio" id="user-role" name="user_role" value="ROLE_ADMIN" /> {{ trans('user.form.administrator') }}
                                </div>
                            </div>
                        </fieldset>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-sm-6 col-sm-offset-2">
                                <button type="submit" class="btn btn-primary">{{ trans('user.form.save_changes') }} </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection