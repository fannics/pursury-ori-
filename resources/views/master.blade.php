<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('metadata')
    <title>@yield('title')</title>
    <link href="{{ asset( settings('app.route_prefix').'/dist/css/front_vendors.css')}}{{ cache_handle() }}" rel="stylesheet" />
    @yield('stylesheets')
    
    <link rel="stylesheet" href="{{ asset(settings('app.route_prefix').'/dist/css/front.css')}}{{ cache_handle() }}" />
    
    <link rel="stylesheet" href="{{ asset(settings('app.route_prefix').get_path_for_front().'/theme.css') }}{{ cache_handle() }}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,600" rel="stylesheet">

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>

    <div id="mobile-selector"></div>

    <div class="custom-menu hidden-xs hidden-sm">
        <div class="logo-wrapper">
            <div class="logo">
                <h1>
                    <a href="{{ route('homepage') }}">
                        <img src="{{asset(settings('app.route_prefix').settings('app.site_logo', '/images/gcs_logo_large.png'))}}" alt="{{ settings('app.app_title') }}">
                    </a>
                </h1>
            </div>
        </div>
        <div class="menu">
            <ul class="menu-items">
                @if (isset($theme_definition['navigation']))
                    @foreach($theme_definition['navigation']['items'] as $navigation_item)
                        <li>
                            <a class="menu-link" title="{{ $navigation_item['title'] }}" href="{{ $navigation_item['url'] }}">{{ $navigation_item['text'] }}</a>
                            @if (isset($navigation_item['children']) && count($navigation_item['children']) > 0)
                                <div class="submenu">
                                    <div class="container">
                                        @foreach($navigation_item['children'] as $child)
                                            <div class="col">
                                                <h2 class="col-title">
                                                    <a class="submenu-header" title="{{ $child['title'] }}" href="{{ $child['url'] }}">{{ $child['text'] }}</a>
                                                </h2>
                                                @if (isset($child['children']) && count($child['children']) > 0)
                                                    <ul>
                                                        @foreach($child['children'] as $grand_children)
                                                            <li><a class="submenu-link" title="{{ $grand_children['title'] }}" href="{{ $grand_children['url'] }}">{{ $grand_children['text'] }}</a></li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
        <div class="form">
            <div class="account-area">
                <a href="#" data-popover=""><i class="fa fa-user fa-lg"></i></a>
                @if (Auth::guest())
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="{{ prefixed_route('/auth/login') }}">{{ trans('master.login') }}</a></li>
                        <li><a href="{{ prefixed_route('/auth/register') }}">{{ trans('master.register') }}</a></li>
                    </ul>
                @else
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="{{ route('profile') }}">{{ trans('master.profile') }}</a></li>
                        <li><a href="{{ prefixed_route('/profile#wishlist') }}">{{ trans('master.wishlist') }}</a></li>
                        <li><a href="{{ route('change_password') }}">{{ trans('master.change_password') }}</a></li>
                        <li><a href="{{ prefixed_route('/auth/logout') }}">{{ trans('master.logout') }}</a></li>
                    </ul>
                @endif
            </div>
            <div class="search-form">
                <form action="{{ route('main_search') }}">
                    <button class="search-btn" type="submit"><i class="fa fa-search fa-lg"></i></button>
                    <input type="text" name="term" id="custom-menu-search-widget" data-url="{{ route('search_autocomplete', ['term' => 'the_term']) }}"/>
                    <div class="search-close-btn" style="display: none">
                        <i class="fa fa-close fa-lg"></i>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="menu-search-results" data-autoshow="true" style="display: none">
    </div>
    <div class="main-container container-fluid {{ isset($index) ? 'on-index' : ''}}">

        <div class="small-navbar visible-xs-block visible-sm-block">
            <div class="left-button-wrapper">
                <button type="button" class="toggle-menu">
                    <i class="fa fa-reorder fa-lg"></i>
                </button>
            </div>
            <div class="brand">
                <a href="{{ route('homepage') }}">
                    <img src="{{asset(settings('app.route_prefix').settings('app.footer_logo', '/images/watermarksincom_resized.png'))}}" alt="{{ settings('app.app_title') }}">
                </a>
            </div>
            <div class="right-button-wrapper">
                <button type="button" class="toggle-menu2">
                    <i class="fa fa-search fa-lg"></i>
                </button>
            </div>
        </div>
        <nav id="my-menu2">
            <div style="width: 100%;">
                <input type="text" name="" id="offcanvas-search-widget" data-url="{{ route('search_autocomplete', ['term' => 'the_term']) }}"/>
                <div class="sidebar-results">
                </div>
            </div>
        </nav>
        <nav id="my-menu">
            <div>
                {!! category_menu_tree($categories_tree) !!}
            </div>
        </nav>
        <section>
            @yield('main_content')
        </section>

        @if (\Session::get('success'))
            <div class="sysalert" data-type="success" alert-title="{{ trans('javascript.alert_title') }}">{{ \Session::get('success') }}</div>
        @endif

        @if (\Session::get('error'))
            <div class="sysalert" data-type="error" alert-title="{{ trans('javascript.alert_title') }}">{{ \Session::get('error') }}</div>
        @endif
    </div>
    <div class="container-fluid" id="new-footer">
        <footer >
            <div class="row">
                <div class="col-xs-12 text-center">
                    <img class="footer-logo" src="{{asset(settings('app.route_prefix').settings('app.footer_logo', '/images/gcs_logo.png'))}}" alt="{{ settings('app.app_title') }}">
                </div>
            </div>
            <div class="row footer-register-form">
                <div class="col-xs-12 col-sm-6 col-sm-offset-3">
                    <div class="row">
                        <div class="col-sm-10 col-sm-offset-1">
                            <div class="row">
                                <form action="{{ prefixed_route('/auth/login') }}">
                                    <div class="form-group">
                                        <div class="col-sm-8">
                                            <input type="text" name="email" id="email" placeholder="{{ trans('master.footer_register_placeholder') }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-4 footer-submit-wrapper text-center">
                                            <button type="submit">{{ trans('master.footer_register_button') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if ( count($setups) > 0 )
              <div class="row text-center">
                {{ trans('master.other_versions') }}
                <br>
                @foreach($setups as $setup)
                  @if ($setup->default_language == 1)
                    <a href="{{ settings('app.url') }}/{{ $setup->country_abre }}">{{ $setup->country }} ({{ $setup->language }})</a>
                  @else
                    <a href="{{ settings('app.url') }}/{{ $setup->country_abre }}/{{ $setup->language_abre }}">{{ $setup->country }} ({{ $setup->language }})</a>
                  @endif 
                  @if($setups->last() === $setup)
                    &nbsp;&nbsp;
                  @else
                    &nbsp;|&nbsp;
                  @endif
                @endforeach
              </div>
            @endif
            <div class="row">
                <div class="col-xs-12 text-center">
                    <div class="copyright">
                        &copy; Copyright {{ settings('app.app_title', '') }} <?php echo date('Y'); ?>. {{ trans('master.copyright') }}
                    </div>
                </div>
            </div>
        </footer>
    </div>
    @include('main/product_modal')
    @include('main/login_modal')

    @yield('other_scripts')
    
      <script>
        var global_productTitleMinLength = "{{ trans('javascript.global_productTitleMinLength') }}";
        var global_productDestinationURLURL = "{{ trans('javascript.global_productDestinationURLURL') }}";
        var global_productPriceNumber = "{{ trans('javascript.global_productPriceNumber') }}";  
        var global_productPriceMin = "{{ trans('javascript.global_productPriceMin') }}";  
        var global_productPreviousPriceNumber = "{{ trans('javascript.global_productPreviousPriceNumber') }}";  
        var global_productPreviousPriceMin = "{{ trans('javascript.global_productPreviousPriceMin') }}";  
        var global_productCouponURLURL = "{{ trans('javascript.global_productCouponURLURL') }}";  
        var global_errorHappened = "{{ trans('javascript.global_errorHappened') }}";  
        var global_updatingAppRoutes = "{{ trans('javascript.global_updatingAppRoutes') }}";  
        var global_updatingSearchEngine = "{{ trans('javascript.global_updatingSearchEngine') }}";
        var global_actionhasFinished = "{{ trans('javascript.global_actionhasFinished') }}";   
        var global_errorUpdatingIndex = "{{ trans('javascript.global_errorUpdatingIndex') }}";   
        var global_errorUpdatingRoutes = "{{ trans('javascript.global_errorUpdatingRoutes') }}";
        var global_errorImportingFile = "{{ trans('javascript.global_errorImportingFile') }}";
        var global_errorUploadingFile = "{{ trans('javascript.global_errorUploadingFile') }}";     
        var global_importingCategories = "{{ trans('javascript.global_importingCategories') }}";     
        var global_updatingCategoryTree = "{{ trans('javascript.global_updatingCategoryTree') }}";     
        var global_actionNotDone = "{{ trans('javascript.global_actionNotDone') }}";     
        var global_errorUpdatingCategoriesTree = "{{ trans('javascript.global_errorUpdatingCategoriesTree') }}";     
        var global_noResults = "{{ trans('javascript.global_noResults') }}";
        var global_showMoreResults = "{{ trans('javascript.global_showMoreResults') }}";      
        var global_accountAlreadyExists = "{{ trans('javascript.global_accountAlreadyExists') }}";      
        var global_passwordsMustMatch = "{{ trans('javascript.global_passwordsMustMatch') }}";      
        var global_newEmailEmail = "{{ trans('javascript.global_newEmailEmail') }}";      
        var global_newEmailConfRequired = "{{ trans('javascript.global_newEmailConfRequired') }}";      
        var global_newEmailConfEmail = "{{ trans('javascript.global_newEmailConfEmail') }}";      
        var global_newEmailConfEqualTo = "{{ trans('javascript.global_newEmailConfEqualTo') }}";  
        var global_newPasswordRequired = "{{ trans('javascript.global_newPasswordRequired') }}";     
        var global_newPasswordMinLength = "{{ trans('javascript.global_newPasswordMinLength') }}";     
        var global_newPasswordConfRequired = "{{ trans('javascript.global_newPasswordConfRequired') }}";     
        var global_newPasswordConfEqualTo = "{{ trans('javascript.global_newPasswordConfEqualTo') }}";     
        var global_activationEmailError = "{{ trans('javascript.global_activationEmailError') }}";     
        var global_sentEmail = "{{ trans('javascript.global_sentEmail') }}";     
        var global_activationEmailErrorTitle = "{{ trans('javascript.global_activationEmailErrorTitle') }}";     
        var global_supplyObject = "{{ trans('javascript.global_supplyObject') }}";     
        var global_specifyMessage = "{{ trans('javascript.global_specifyMessage') }}";     
        var global_testEmailSent = "{{ trans('javascript.global_testEmailSent') }}";     
        var global_importingProducts = "{{ trans('javascript.global_importingProducts') }}";     
        var global_extendRequired = "{{ trans('javascript.global_extendRequired') }}"; 
        var global_extendRemote = "{{ trans('javascript.global_extendRemote') }}";
        var global_extendEmail = "{{ trans('javascript.global_extendEmail') }}";
        var global_extendURL = "{{ trans('javascript.global_extendURL') }}";
        var global_extendDate = "{{ trans('javascript.global_extendDate') }}";
        var global_extendDateISO = "{{ trans('javascript.global_extendDateISO') }}";
        var global_extendNumber = "{{ trans('javascript.global_extendNumber') }}";
        var global_extendDigits = "{{ trans('javascript.global_extendDigits') }}";
        var global_extendCreditCard = "{{ trans('javascript.global_extendCreditCard') }}";
        var global_extendEqualTo = "{{ trans('javascript.global_extendEqualTo') }}";
        var global_extendExtension = "{{ trans('javascript.global_extendExtension') }}";
        var global_extendMaxLength = "{{ trans('javascript.global_extendMaxLength') }}";
        var global_extendMinLength = "{{ trans('javascript.global_extendMinLength') }}";
        var global_extendRangeLength = "{{ trans('javascript.global_extendRangeLength') }}";
        var global_extendRange = "{{ trans('javascript.global_extendRange') }}";
        var global_extendMax = "{{ trans('javascript.global_extendMax') }}";
        var global_extendMin = "{{ trans('javascript.global_extendMin') }}";
        var global_extendNIFES = "{{ trans('javascript.global_extendNIFES') }}";
        var global_extendNIEES = "{{ trans('javascript.global_extendNIEES') }}";
        var global_extendCIFES = "{{ trans('javascript.global_extendCIFES') }}";
      </script>    
    
      <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
      <script src="{{ asset( settings('app.route_prefix').'/dist/js/front_vendors.js') }}{{ cache_handle() }}"></script>
      <script src="{{ asset( settings('app.route_prefix').'/dist/js/front_app.js') }}{{ cache_handle() }}"></script>
      <script type="text/javascript">
        window.app_prefix = '{{ settings('app.route_prefix') }}'  + '{{ $thisCountry }}' + '{{ $thisLanguage }}';
      </script>
    @yield('javascripts')

</body>
</html>
