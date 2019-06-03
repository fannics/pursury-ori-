@extends('master')

@section('metadata')
    <meta NAME="robots" CONTENT="index, follow">
    <meta name="description" content="Cambie la configuración de las notificaciones">
@endsection

@section('title', settings('app.app_title').' - '.trans('main.changeNotifications.change_notifications_title'))

@section('main_content')
    <div class="row profile-info">
        <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
            <h3 class="text-center">{{ trans('main.changeNotifications.change_notifications_title') }}</h3>
            <form action="{{ route('handle_change_notifications') }}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group" style="margin: 20px 0;">
                        <input
                                type="checkbox"
                                name="subscribe_to_newsletter"
                                id="subscribe-to-newsletter"
                                {{ $user->newsletter ? 'checked="checked"' : '' }}
                                value="1"
                                />
                        {{ trans('main.changeNotifications.change_notifications_text') }}
                </div>
                <div class="form-group">
                    <button class="def-btn" type="submit" value=""><i class="fa fa-save"></i> {{ trans('main.changeNotifications.save_changes') }}</button>
                </div>
                <div class="form-group profile-buttons">
                    <a class="prim-bordered" href="{{ route('profile_edit') }}">{{ trans('main.changeNotifications.go_back') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection


