<?php

	return [

        'env' => env('APP_ENV', 'production'),

        /*
        |--------------------------------------------------------------------------
        | Application Debug Mode
        |--------------------------------------------------------------------------
        |
        | When your application is in debug mode, detailed error messages with
        | stack traces will be shown on every error that occurs within your
        | application. If disabled, a simple generic error page is shown.
        |
        */
		'debug' => env('APP_DEBUG'),

		/*
        |--------------------------------------------------------------------------
        | Application URL
        |--------------------------------------------------------------------------
        |
        | This URL is used by the console to properly generate URLs when using
        | the Artisan command line tool. You should set this to the root of
        | your application so that it is used when running Artisan tasks.
        |
        */

		'url' => env('APP_URL',''),

		/*
        |--------------------------------------------------------------------------
        | Application Timezone
        |--------------------------------------------------------------------------
        |
        | Here you may specify the default timezone for your application, which
        | will be used by the PHP date and date-time functions. We have gone
        | ahead and set this to a sensible default for you out of the box.
        |
        */

		'timezone' => 'UTC',

		/*
        |--------------------------------------------------------------------------
        | Application Locale Configuration
        |--------------------------------------------------------------------------
        |
        | The application locale determines the default locale that will be used
        | by the translation service provider. You are free to set this value
        | to any of the locales which will be supported by the application.
        |
        */

		'locale' => env('LOCALE', 'es'),

		/*
        |--------------------------------------------------------------------------
        | Application Fallback Locale
        |--------------------------------------------------------------------------
        |
        | The fallback locale determines the locale to use when the current one
        | is not available. You may change the value to correspond to any of
        | the language folders that are provided through your application.
        |
        */

		'fallback_locale' => 'en',

		/*
        |--------------------------------------------------------------------------
        | Encryption Key
        |--------------------------------------------------------------------------
        |
        | This key is used by the Illuminate encrypter service and should be set
        | to a random, 32 character string, otherwise these encrypted strings
        | will not be safe. Please do this before deploying an application!
        |
        */

		'key' => env('APP_KEY', 'SomeRandomString'),

		'cipher' => 'AES-128-CBC',

		/*
        |--------------------------------------------------------------------------
        | Logging Configuration
        |--------------------------------------------------------------------------
        |
        | Here you may configure the log settings for your application. Out of
        | the box, Laravel uses the Monolog PHP logging library. This gives
        | you a variety of powerful log handlers / formatters to utilize.
        |
        | Available Settings: "single", "daily", "syslog", "errorlog"
        |
        */

		'log' => 'daily',

		/*
        |--------------------------------------------------------------------------
        | Autoloaded Service Providers
        |--------------------------------------------------------------------------
        |
        | The service providers listed here will be automatically loaded on the
        | request to your application. Feel free to add your own services to
        | this array to grant expanded functionality to your applications.
        |
        */

		'providers' => [

			/*
             * Laravel Framework Service Providers...
             */
			'Illuminate\Auth\AuthServiceProvider',
			'Illuminate\Bus\BusServiceProvider',
			'Illuminate\Cache\CacheServiceProvider',
			'Illuminate\Foundation\Providers\ConsoleSupportServiceProvider',
			'Illuminate\Cookie\CookieServiceProvider',
			'Illuminate\Database\DatabaseServiceProvider',
			'Illuminate\Encryption\EncryptionServiceProvider',
			'Illuminate\Filesystem\FilesystemServiceProvider',
			'Illuminate\Foundation\Providers\FoundationServiceProvider',
			'Illuminate\Hashing\HashServiceProvider',
			'Illuminate\Mail\MailServiceProvider',
			'Illuminate\Pagination\PaginationServiceProvider',
			'Illuminate\Pipeline\PipelineServiceProvider',
			'Illuminate\Queue\QueueServiceProvider',
			'Illuminate\Redis\RedisServiceProvider',
			'Illuminate\Auth\Passwords\PasswordResetServiceProvider',
			'Illuminate\Session\SessionServiceProvider',
			//'Illuminate\Translation\TranslationServiceProvider',
      		'ProjectCarrasco\Providers\CustomTranslationServiceProvider',
			'Illuminate\Validation\ValidationServiceProvider',
			'Illuminate\View\ViewServiceProvider',
            'Illuminate\Broadcasting\BroadcastServiceProvider',

			/*
             * Application Service Providers...
             */
			'ProjectCarrasco\Providers\AppServiceProvider',
			'ProjectCarrasco\Providers\RouteServiceProvider',
			'ProjectCarrasco\Providers\ConfigServiceProvider',
			'ProjectCarrasco\Providers\EventServiceProvider',
			'Laravel\Socialite\SocialiteServiceProvider',
			'Rap2hpoutre\LaravelLogViewer\LaravelLogViewerServiceProvider',
			'Efriandika\LaravelSettings\SettingsServiceProvider',

			/*
             * Custom Service Providers
             */
			'ProjectCarrasco\Providers\FrontViewGlobals',
			'ProjectCarrasco\Providers\MainServiceProvider',

           'Collective\Html\HtmlServiceProvider',
            Maatwebsite\Excel\ExcelServiceProvider::class,
        ],

		/*
        |--------------------------------------------------------------------------
        | Class Aliases
        |--------------------------------------------------------------------------
        |
        | This array of class aliases will be registered when this application
        | is started. However, feel free to register as many as you wish as
        | the aliases are "lazy" loaded so they don't hinder performance.
        |
        */

		'aliases' => [

			'App'       => 'Illuminate\Support\Facades\App',
			'Artisan'   => 'Illuminate\Support\Facades\Artisan',
			'Auth'      => 'Illuminate\Support\Facades\Auth',
			'Blade'     => 'Illuminate\Support\Facades\Blade',
			'Bus'       => 'Illuminate\Support\Facades\Bus',
			'Cache'     => 'Illuminate\Support\Facades\Cache',
			'Config'    => 'Illuminate\Support\Facades\Config',
			'Cookie'    => 'Illuminate\Support\Facades\Cookie',
			'Crypt'     => 'Illuminate\Support\Facades\Crypt',
			'DB'        => 'Illuminate\Support\Facades\DB',
			'Eloquent'  => 'Illuminate\Database\Eloquent\Model',
      'Form' => 'Collective\Html\FormFacade',
			'Event'     => 'Illuminate\Support\Facades\Event',
			'File'      => 'Illuminate\Support\Facades\File',
			'Hash'      => 'Illuminate\Support\Facades\Hash',
      'Html' => 'Collective\Html\HtmlFacade',
			'Input'     => 'Illuminate\Support\Facades\Input',
			'Inspiring' => 'Illuminate\Foundation\Inspiring',
			'Lang'      => 'Illuminate\Support\Facades\Lang',
			'Log'       => 'Illuminate\Support\Facades\Log',
			'Mail'      => 'Illuminate\Support\Facades\Mail',
			'Password'  => 'Illuminate\Support\Facades\Password',
			'Queue'     => 'Illuminate\Support\Facades\Queue',
			'Redirect'  => 'Illuminate\Support\Facades\Redirect',
			'Redis'     => 'Illuminate\Support\Facades\Redis',
			'Request'   => 'Illuminate\Support\Facades\Request',
			'Response'  => 'Illuminate\Support\Facades\Response',
			'Route'     => 'Illuminate\Support\Facades\Route',
			'Schema'    => 'Illuminate\Support\Facades\Schema',
			'Session'   => 'Illuminate\Support\Facades\Session',
			'Storage'   => 'Illuminate\Support\Facades\Storage',
			'URL'       => 'Illuminate\Support\Facades\URL',
			'Validator' => 'Illuminate\Support\Facades\Validator',
			'View'      => 'Illuminate\Support\Facades\View',
			'Socialite' => 'Laravel\Socialite\Facades\Socialite',
			'Settings'  => 'Efriandika\LaravelSettings\Facades\Settings',
            'Excel' => Maatwebsite\Excel\Facades\Excel::class,

		],

		/**
		 * Custom configuration values
		 */
		'app_title' => env('SITE_NAME', 'Tienda de Prueba'),

//		'route_prefix' => '/es',
		'route_prefix' => env('SITE_PREFIX', ''),

//		'elasticsearch_host' => 'https://ent-asolenzal.rhcloud.com:443',
		'elasticsearch_host' => '',

//		'elasticsearch_index_name' => 'entrenamiento',

		'elasticsearch_index_name' => '',
		'disable_cache' => true,

//		'image_processor' => 'thumbor',
		'image_processor' => '',

//		'thumbor_address' => 'http://127.0.0.1:8888/',
		'thumbor_address' => '',

		'thumbnail_size_for_tile' => '200x200',

		'product_file_image_size' => '700x900',

		'currency_name' =>  'Euro',
		'currency_html_code' =>  '&euro;',
		'currency_location' =>  'right',

		'money_decimal_digits' =>  '2',
		'money_decimal_separator' =>  '.',
		'money_thousands_separator' =>  ',',

		'multicolor_value' => 'Multicolor',

		'base_lang' => env('LOCALE', 'es'),

		'fetch_translations' => false
	];
