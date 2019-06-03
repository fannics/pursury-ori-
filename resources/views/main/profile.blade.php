@extends('master')

@section('metadata')
    <meta NAME="robots" CONTENT="index, follow">
    <meta name="description" content="">
@endsection

@section('title', settings('app.app_title').' - '.trans('main.profile.user_profile_title'))

@section('main_content')
    <div class="row profile-info">
        <div class="col-sm-4 col-sm-offset-4">
            <div class="text-center">
                <div class="profile-picture">
                    @if($user->profile_photo_url)
                        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                    @else
                        <img src="{{ asset(settings('app.route_prefix').'/images/default-profile.png') }}" alt="{{ $user->name }}">
                    @endif
                </div>
                <h3>{{ $user->name }}</h3>
            </div>
            <div class="text-center">
                <a href="{{ route('profile_edit') }}">{{ trans('main.profile.profile_edit') }}</a>
            </div>
        </div>
    </div>
    <div class="row profile-wishlist-wrapper" style="margin-top: 30px;;">
        <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h3 id="wishlist">{{ trans('main.profile.wishlist') }}</h3>
                </div>
            </div>                                                                                          
            @if ($wishlist)
                <div class="products-list">
                    @foreach($wishlist as $wishlist_item)
                        @if ($wishlist_item->product)
                            @include('main/wishlist_tile', ['product' => $wishlist_item->product, 'wishlist_is_the_source' => true])
                        @endif
                    @endforeach
                </div>
            @endif
            <p class="text-center" style="{{ $wishlist->count() > 0 ? 'display:none;' : '' }}">{{ trans('main.profile.wishlist_no_products') }}</p>
        </div>
    </div>
@endsection