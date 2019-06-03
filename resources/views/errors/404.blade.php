@extends('master')

@section('title', settings('app.app_title').' - '.trans('errors.404.not_found_page') )

@section('main_content')
    <div class="row error-page">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-12 text-center">
                    <h3 class="text-center">{{ trans('errors.404.we_sorry') }}</h3>
                    <h1 class="big">404</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-4 col-md-offset-4">
                    <input type="text" class="form-control input-lg search-widget" id="404-search-widget" />
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 text-center">
                    <h3><a href="{{ route('homepage') }}">{{ trans('errors.404.home') }}</a></h3>
                </div>
            </div>
        </div>
    </div>
@endsection
