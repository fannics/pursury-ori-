@extends('master')

@section('metadata')
    <meta NAME="robots" CONTENT="index, follow">
    <meta name="description" content="{{ trans('main.index.meta_description') }}">  

    <meta property="og:title" content="{{ settings('app.app_title').' - '.trans('main.index.most_popular') }}" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="{{ asset(settings('app.route_prefix').'/images/watermarksincom_resized.png') }}" />
    <meta property="og:description" content="{{ trans('main.index.meta_description') }}" />
    <meta property="og:site_name" content="{{ settings('app.app_title') }}" />
    
    <!-- HrefLang tags -->
    @foreach($setups as $setup)
      @if ($setup->default_language == 0) <link rel="alternate" hreflang="{{ $setup->language_abre }}-{{ $setup->country_abre }}" href="{{ url('') }}{{ settings('app.route_prefix') }}/{{ $setup->country_abre }}/{{ $setup->language_abre }}" /> @else <link rel="alternate" hreflang="{{ $setup->language_abre }}-{{ $setup->country_abre }}" href="{{ url('') }}{{ settings('app.route_prefix') }}/{{ $setup->country_abre }}" /> @endif      
    @endforeach

@endsection
@section('title', settings('app.app_title').' - '.trans('main.new_homepage.app_title_slogan') )

@section('main_content')
    <div class="row front-top" style="background-image: url('{{ settings('app.route_prefix').settings('app.homepage_background', asset('/images/home-back.jpg')) }}')">
        <div class="col-xs-12">
            <div class="front-top-header">
                <div class="row header">
                    <div class="col-xs-12 col-sm-6 col-sm-offset-3 text-center">
                        <h2 class="front-top-header-text">
                            @if (isset($theme_definition['home_top']['title']) && $theme_definition['home_top']['title'])
                                {{ $theme_definition['home_top']['title'] }}
                            @endif
                        </h2>
                    </div>
                </div>
                @if (isset($theme_definition['home_top']['buttons']) && count($theme_definition['home_top']['buttons']) > 0)
                    <div class="row front-round-buttons">
                        <div class="col-xs-12 text-center">
                            @foreach($theme_definition['home_top']['buttons'] as $button)
                                <a class="top-header-button" title="{{ $button['title'] }}" href="{{ $button['url'] }}" class="front-top-button">{{ $button['text'] }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="row front-bottom">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-12">
                    <div class="header front-bottom-header">
                        @if(isset($theme_definition['home_bottom']['title']) && $theme_definition['home_bottom']['title'])
                            {{ $theme_definition['home_bottom']['title'] }}
                        @else
                            {{ trans('main.new_homepage.home_bottom_title') }}
                        @endif
                    </div>
                    @if (isset($theme_definition['home_bottom']['buttons']) && count($theme_definition['home_bottom']['buttons']) > 0)
                    <div class="row front-round-buttons">
                        <div class="col-xs-12 text-center">
                            @foreach($theme_definition['home_bottom']['buttons'] as $button)
                                <a title="{{ $button['title'] }}" class="top-header-button bottom" href="{{ $button['url'] }}" class="front-top-button">{{ $button['text'] }}</a>
                            @endforeach
                        </div>
                    </div>
                    @endif                                               
                </div>
            </div>
            @if (isset($theme_definition['home_bottom']['navigation']) && isset($theme_definition['home_bottom']['navigation']['items']) && count($theme_definition['home_bottom']['navigation']['items']) > 0)
            <div class="row">
                <div class="col-xs-12 col-sm-10 col-sm-offset-1">
                    <div class="categories-list">
                        <div class="columns-container columns4">
                            @foreach($theme_definition['home_bottom']['navigation']['items'] as $col)
                                <div class="col">
                                    <div class="col-header bottom-section-header">
                                        <a href="{{ $col['url'] }}" title="{{ $col['title'] }}">{{ $col['text'] }}</a>
                                    </div>
                                    @if (isset($col['children']) && count($col['children']) > 0)
                                        <ul class="subtitles">
                                            @foreach($col['children'] as $child)
                                                <li><a class="bottom-section-link" title="{{ $child['title'] }}" href="{{ $child['url'] }}">{{ $child['text'] }}</a></li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection

@section('javascripts')

@endsection