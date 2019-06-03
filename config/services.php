<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	'mailgun' => [
		'domain' => '',
		'secret' => '',
	],

	'mandrill' => [
		'secret' => '',
	],

	'ses' => [
		'key' => '',
		'secret' => '',
		'region' => 'us-east-1',
	],

	'stripe' => [
		'model'  => 'User',
		'secret' => '',
	],
	'google' => [
		'client_id' => '261535776901-i62sbffkbb1sfromdlil8g6j097g5p5s.apps.googleusercontent.com',
		'client_secret' => '0Y0pYTr-dUoP9pVf7UVKy5BF',
		'redirect' => 'https://www.pursury.com/login-callback/google'
	],
	'facebook' => [
		'client_id' => '524283717757964',
		'client_secret' => '8595fdcdf857808673cd801542e0c07b',
		'redirect' => 'https://www.pursury.com/login-callback/facebook'
	],
    'rollbar' => [
        'access_token' => env('ROLLBAR_TOKEN','babb84ff188d4818b3b9fd05d06e975e'),
        'level' => env('ROLLBAR_LEVEL','none'),
    ],

];
