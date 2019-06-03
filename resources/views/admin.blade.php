<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>{{ settings('app.app_title') }} - {{ trans('admin.index.dashboard') }}</title>

    <!-- Bootstrap -->
    <link href="{{ asset(settings('app.route_prefix').'/dist/css/admin_vendors.css')}}{{ cache_handle() }}" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset(settings('app.route_prefix').'/dist/css/admin.css')}}{{ cache_handle() }}" />

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,600" rel="stylesheet">

    @yield('stylesheets')

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container-fluid">
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ route('admin_home') }}">
                    <img src="{{asset(settings('app.route_prefix').settings('app.site_logo', '/images/gcs_logo_large.png'))}}" alt="{{ settings('app.app_title') }}" />
                </a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-globe"></i> {{ trans('admin.uppernavbar.countries_languages') }} <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ route('admin_setups_list') }}">{{ trans('admin.uppernavbar.countries_languages_master_setup') }}</a></li>
                            @foreach ($currentSetups as $aSetup)
                              <li><a href="{{ settings('app.route_prefix') }}/{{ $aSetup->country_abre }}/{{ $aSetup->language_abre }}/admin">{{ trans('admin.uppernavbar.countries_languages_go_to') }} {{ $aSetup->country }} >> {{ $aSetup->language }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-language"></i> {{ trans('admin.uppernavbar.translations') }} <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ route('admin_translations_list') }}">{{ trans('admin.uppernavbar.translations_list') }}</a></li>
                            <li><a href="{{ route('admin_translations_import') }}">{{ trans('admin.uppernavbar.translations_import') }}</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-level-down"></i> {{ trans('admin.uppernavbar.categories') }} <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ route('admin_categories_list') }}">{{ trans('admin.uppernavbar.categories_list') }}</a></li>
                            <li><a href="{{ route('admin_categories_import') }}">{{ trans('admin.uppernavbar.categories_import') }}</a></li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa fa-road"></i> {{ trans('admin.uppernavbar.brands') }} & {{ trans('admin.uppernavbar.stores') }} <span class="caret"></span></a>
                        <ul class="dropdown-menu" >

                            <li class="dropdown" >
                                <a href="{{ route('admin.stores.index') }}"><i class="fa fa fa-road"></i> {{ trans('admin.uppernavbar.stores') }}</a>
                                <ul class="dropdown-menu" style=" top: 0;left: 100%;margin-top: -1px;">
                                   <li> <a href="{{ route('admin.stores.index') }}">{{ trans('admin.uppernavbar.stores_list') }}</a></li>
                                    <li><a href="{{ route('admin.stores.importView') }}">{{ trans('admin.uppernavbar.stores_import') }}</a></li>
                                </ul>
                            </li>

                            <li class="dropdown">
                                <a href="{{ route('admin.brands.index') }}"><i class="fa fa fa-briefcase"></i> {{ trans('admin.uppernavbar.brands') }}</a>
                                <ul class="dropdown-menu" style=" top: 0;left: 100%;margin-top: -1px;">
                                    <li><a href="{{ route('admin.brands.index') }}">{{ trans('admin.uppernavbar.brands_list') }}</a></li>
                                    <li><a href="{{ route('admin.brands.importView') }}">{{ trans('admin.uppernavbar.brands_import') }}</a></li>
                                </ul>
                            </li>

                         </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bicycle"></i> {{ trans('admin.uppernavbar.products') }} <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ route('admin_product_list') }}">{{ trans('admin.uppernavbar.products_list') }}</a></li>
                            <li><a href="{{ route('admin_product_import') }}">{{ trans('admin.uppernavbar.products_import') }}</a></li>
                        </ul>
                    </li>
                    <li><a href="{{ route('admin_users') }}"><i class="fa fa-users"></i> {{ trans('admin.uppernavbar.users') }}<span class="sr-only">({{ trans('admin.uppernavbar.users_current') }})</span></a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-cogs"></i> {{ trans('admin.uppernavbar.configuration') }} <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            {{--<li><a href="{{ route('admin_front_menu') }}">{{ trans('admin.uppernavbar.configuration_main_menu') }}</a></li>--}}
                            <li><a href="{{ route('admin_settings') }}">{{ trans('admin.uppernavbar.configuration_configuration') }}</a></li>
                            <li><a href="{{ route('admin_homepage_edit') }}">{{ trans('admin.uppernavbar.configuration_theme_homepage') }}</a></li>
                            <li><a href="{{ route('admin_color_codes') }}">{{ trans('admin.uppernavbar.configuration_color_codes') }}</a></li>
                            <li><a href="{{ route('search_engine_index') }}">{{ trans('admin.uppernavbar.configuration_search_engine') }}</a></li>
                            <li><a href="{{ route('search_engine_latest') }}">{{ trans('admin.uppernavbar.configuration_latest_searches') }}</a></li>
                            <li><a href="{{ route('admin_show_logs') }}">{{ trans('admin.uppernavbar.configuration_errors_registration') }}</a></li>
                            <li><a href="{{ route('admin_imports_done') }}">{{ trans('admin.uppernavbar.configuration_import_history') }}</a></li>
                            <li><a href="{{ route('email_templates_config') }}">{{ trans('admin.uppernavbar.configuration_email_templates') }}</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="{{ route('homepage') }}"><i class="fa fa-star"></i> {{ trans('admin.uppernavbar.go_site') }}</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-user"></i> {{ Auth::user()->name }} <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ prefixed_route('/auth/logout') }}">{{ trans('admin.uppernavbar.logout') }}</a></li>
                        </ul>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
    @if (\Session::get('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="{{ trans('admin.message.close') }}"><span aria-hidden="true">&times;</span></button>
            {{ \Session::get('success') }}
        </div>
    @endif
    @if (\Session::get('notice'))
        <div class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="{{ trans('admin.message.close') }}"><span aria-hidden="true">&times;</span></button>
            {{ \Session::get('notice') }}
        </div>
    @endif
    @if (\Session::get('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="{{ trans('admin.message.close') }}"><span aria-hidden="true">&times;</span></button>
            {{ \Session::get('error') }}
        </div>
    @endif
    @foreach ($currentSetups as $aSetup)
      @if ($aSetup->country_abre == str_replace('/','',$thisCountry))
        @if ( ($thisLanguage === '') && ($aSetup->default_language == 1) )
          <strong class="redText"><i class="fa fa-globe"></i> {{ trans('admin.interface_for') }} {{ $aSetup->country }}/{{ $aSetup->language }}</strong>
          <br clear="all"/><br>
        @endif
        @if ($aSetup->language_abre == str_replace('/','',$thisLanguage))
          <strong class="redText"><i class="fa fa-globe"></i> {{ trans('admin.interface_for') }} {{ $aSetup->country }}/{{ $aSetup->language }}</strong>
          <br clear="all"/><br>
        @endif
      @endif
    @endforeach

    @yield('main_content')
    <footer id="main-footer">
        Copyright {{ Config::get('app')['app_title'] }} - &copy <?php echo date('Y'); ?>
    </footer>
</div>

@yield('previous_vendor_javascripts')

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
<script src="{{ asset(settings('app.route_prefix').'/dist/js/admin_vendors.js') }}{{ cache_handle() }}"></script>
<script src="{{ asset(settings('app.route_prefix').'/dist/js/admin_app.js') }}{{ cache_handle() }}"></script>
<script type="text/javascript">
    window.app_prefix = '{{ settings('app.route_prefix') }}'  + '{{ $thisCountry }}' + '{{ $thisLanguage }}';
</script>
@yield('javascripts')

<!-- Include all compiled plugins (below), or include individual files as needed -->
</body>
</html>
