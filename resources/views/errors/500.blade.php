@extends('master')

@section('title', settings('app.app_title').' - '. trans('errors.500.there_error') )

@section('main_content')
    <div class="row error-page error-page-500">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-12 text-center">
                    <img src="{{asset(settings('app.route_prefix').'/images/error.png')}}" alt="error">
                    <h3 class="text-center">{{ trans('errors.500.we_sorry')  }}</h3>
                    <h1 class="big">500</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-4 col-md-offset-4">
                    <p class="text-center">{{ trans('errors.500.our_admins')  }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 text-center">
                    <h3><a href="{{ route('homepage') }}">{{ trans('errors.500.home')  }}</a></h3>
                </div>
            </div>
        </div>
    </div>
@endsection