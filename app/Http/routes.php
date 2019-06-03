<?php

  $route_prefixes = get_current_prefixes(true);
  $route_prefix = \Config::get('app')['route_prefix'].'/';
  if ( !empty($route_prefixes[0]) ) {
    $route_prefix = $route_prefix.$route_prefixes[0].'/';
  }
  if ( !empty($route_prefixes[1]) ) {
    $route_prefix = $route_prefix.$route_prefixes[1].'/';
  }

  /********* PUBLIC ROUTES *********/
  Route::group(['prefix' => $route_prefix.'/'],
    function() use ($route_prefix) {

      Route::get('/',
        [
          'as' => 'homepage',
          'uses' => 'MainController@homepageAction'
	     ]
      );

      Route::get('/image-resizer/',
        [
          'as' => 'internal_on_demand_image_resizer',
		      'uses' => 'MainController@onDemandImageResizeAction'
        ]
      );

      Route::post('/get-tags',
        [
		      'as' => 'post_get_tags',
		      'uses' => 'MainController@getTagsAction'
        ]
      );

      Route::post('/get-children',
        [
		      'as' => 'post_get_children',
		      'uses' => 'ProductController@getChildrenAction'
        ]
      );

      Route::get('/popular/{page?}',
        array(
		      'as' => 'catalog',
		      'uses' => 'MainController@index'
        )
      )->where('page', '[0-9]+');

      Route::get('/autocomplete/',
        array(
          'as' => 'search_autocomplete',
          'uses' => 'MainController@searchAutocomplete'
	      )
      );

      Route::get('/login/{provider}',
        array(
		      'as' => 'social_login_redirect',
		      'uses' => 'MainController@socialLoginRedirect'
	      )
      );

      Route::get('/login-callback/google',
        array(
          'as' => 'social_login_callback_google',
		      'uses' => 'MainController@socialLoginCallbackGoogle'
        )
      );

      Route::get('/login-callback/facebook',
        array(
          'as' => 'social_login_callback_facebook',
          'uses' => 'MainController@socialLoginCallbackFacebook'
	      )
      );

      Route::get('/search/{page?}',
        array(
          'as' => 'main_search',
		      'uses' => 'MainController@searchPage'
        )
      )->where('page', '[0-9]+');

      Route::get('/not-found',
        array(
          'as' => '404_error',
		      'uses' => 'MainController@action404'
        )
      );

      Route::get('/profile',
        array(
          'as' => 'profile',
          'uses' => 'ProfileController@profile',
          'middleware' => 'auth',
	      )
      );

      Route::get('/profile/edit',
        array(
          'as' => 'profile_edit',
		      'uses' => 'ProfileController@profileEdit',
          'middleware' => 'auth',
	      )
      );

      Route::post('/profile/edit',
        array(
		      'as' => 'handle_profile_edit',
		      'uses' => 'ProfileController@handleProfileEdit',
		      'middleware' => 'auth',
        )
      );

      Route::get('/change-password',
        array(
          'as' => 'change_password',
          'uses' => 'ProfileController@changePassword',
          'middleware' => 'auth',
        )
      );

      Route::post('/change-password',
        array(
          'as' => 'handle_change_password',
		      'uses' => 'ProfileController@handleChangePassword',
		      'middleware' => 'auth',
	     )
      );

      Route::get('/change-email',
        array(
          'as' => 'change_email',
          'uses' => 'ProfileController@changeEmail',
          'middleware' => 'auth',
        )
      );

      Route::post('/change-email',
        array(
          'as' => 'handle_change_email',
          'uses' => 'ProfileController@handleChangeEmail',
          'middleware' => 'auth',
	      )
      );

      Route::get('/change-notifications',
        array(
          'as' => 'change_notifications',
		      'uses' => 'ProfileController@changeNotifications',
		      'middleware' => 'auth',
	      )
      );

      Route::post('/change-notifications',
        array(
          'as' => 'handle_change_notifications',
		      'uses' => 'ProfileController@handleChangeNotifications',
		      'middleware' => 'auth',
        )
      );

      Route::get('/wishlist',
        array(
          'as' => 'wishlist',
		      'uses' => 'MainController@wishlist',
		      'middleware' => 'auth',
        )
      );

      Route::get('/test-mail',
        array(
		      'as' => 'test_mail',
		      'uses' => 'MainController@testMail',
        )
      );

      Route::post('/wishlist-item',
        array(
          'as' => 'wishlist_item',
		      'uses' => 'ProfileController@wishlistToggle',
		      'middleware' => 'auth'
        )
      );

      Route::post('/wishlist-remove',
        array(
          'as' => 'wishlist_remove',
		      'uses' => 'MainController@wishlistRemove',
		      'middleware' => 'auth'
        )
      );

      Route::get('/category/filter/{category}/{filter}',
        array(
          'as' => 'category_filter',
          'uses' => 'MainController@renderFilter'
        )
      );

      Route::get('/category/product-filter/{category}/{filter}',
        array(
		      'as' => 'category_product_filter',
		      'uses' => 'MainController@renderProductFilter'
        )
      )->where('category', '[0-9]+');

      Route::post('/buy/product',
        array(
          'as' => 'product_shop',
		      'uses' => 'MainController@postProductPage'
        )
      );

      Route::get('/thumbnail/{size}',
        array(
          'as' => 'thumbor_end',
		      'uses' => 'MainController@thumborEnd'
	       )
      );

      Route::post('/activation-needed',
        [
          'as' => 'post_activation',
          'uses' => 'Auth\ActivationController@getActivationNeeded'
	      ]
      );

      Route::get('/auth/activation/{token}',
        [
		      'as' => 'get_activation',
		      'uses' => 'Auth\ActivationController@getActivation'
        ]
      );

      Route::get('/auth/reset/{token}',
        [
		      'as' => 'get_password_reset',
		      'uses' => 'Auth\PasswordController@getReset'
        ]
      );

      Route::post('/auth/reset/{token}',
        [
		      'as' => 'post_password_reset',
		      'uses' => 'Auth\PasswordController@postReset'
        ]
      );

      Route::get('/theme.css',
        [
          'as' => 'get_enabled_theme',
		      'uses' => 'MainController@themeAction'
        ]
      );

        Route::get('auth/register',['uses' => 'Auth\RegisterController@showRegistrationForm']);
        Route::post('auth/register',['uses' => 'Auth\RegisterController@postRegister']);

        Route::get('auth/activation',['uses' => 'Auth\ActivationController@getActivation']);
        Route::get('auth/activation-needed',['uses' => 'Auth\ActivationController@getActivationNeeded']);

        Route::post('auth/login',['uses' => 'Auth\LoginController@postLogin']);
        Route::get('auth/login',['uses' => 'Auth\LoginController@getLogin']);
        Route::get('auth/logout',['uses' => 'Auth\LoginController@getLogout']);

        Route::post('password/email',['uses' => 'Auth\ForgotPasswordController@postEmail']);
        Route::post('password/reset',['uses' => 'Auth\ForgotPasswordController@postReset']);

        Route::get('auth/check-email',['uses' => 'Auth\LoginController@getCheckEmail']);

    }
  );


  /********* ADMIN ROUTES *********/
  Route::group(['middleware' => 'is_admin', 'prefix' => $route_prefix.'admin'],
    function() {


      // Admin home
      Route::get('/',
        array(
		      'as' => 'admin_home',
		      'uses' => 'AdminController@index'
        )
      );


      // Menu
      Route::get('/menu',
        array(
          'as' => 'admin_front_menu',
          'uses' => 'AdminController@menuConfiguration'
        )
      );

      Route::post('/menu',
        array(
          'as' => 'admin_front_menu_handle',
		      'uses' => 'AdminController@handleMenuConfiguration'
	      )
      );


      //Homepage
      Route::get('/homepage',
        [
          'as' => 'admin_homepage_edit',
          'uses' => 'HomepageController@editAction'
        ]
      );

      Route::get('/homepage/definition',
        [
          'as' => 'admin_homepage_definition',
          'uses' => 'HomepageController@definitionAction'
        ]
      );

      Route::post('/homepage/logo',
        [
		      'as' => 'admin_homepage_upload_logo',
		      'uses' => 'HomepageController@uploadLogoAction'
        ]
      );

      Route::post('/homepage/small-logo',
        [
		      'as' => 'admin_homepage_upload_logo',
		      'uses' => 'HomepageController@uploadSmallLogoAction'
        ]
      );

      Route::post('/homepage/home-background',
        [
          'as' => 'admin_homepage_upload_home_background',
          'uses' => 'HomepageController@uploadHomeBackgroundAction'
        ]
      );

      Route::post('/homepage/update',
        [
          'as' => 'admin_homepage_definition_update',
		      'uses' => 'HomepageController@handleUpdateAction'
        ]
      );

      Route::post('/homepage/check-url',
        [
          'as' => 'admin_homepage_check_url',
		      'uses' => 'HomepageController@validateUrlAction'
        ]
      );

      Route::get('/homepage/categories',
        [
          'as' => 'admin_homepage_categories',
		      'uses' => 'HomepageController@getCategoriesForNavigationAction'
        ]
      );


      // Users
      Route::get('/users/{page?}',
        array(
          'as' => 'admin_users',
          'uses' => 'UserController@index'
        )
      )->where('page', '[0-9]+');

      Route::get('/users/edit/{id?}',
        array(
		      'as' => 'admin_users_edit_form',
		      'uses' => 'UserController@form'
        )
      );

      Route::post('/users/batch',
        array(
		      'as' => 'admin_users_batch',
		      'uses' => 'UserController@batchAction'
        )
      );

      Route::post('/users/edit/{id?}',
        array(
          'as' => 'admin_users_edit_form_process',
		      'uses' => 'UserController@formProcess'
        )
      );


      //Master setup
      Route::get('/setups/{page?}',
        array(
		      'as' => 'admin_setups_list',
		      'uses' => 'SetupController@index'
        )
      )->where('page', '[0-9]+');

      Route::get('/setups/edit/{id}',
        array(
          'as' => 'admin_setups_edit',
          'uses' => 'SetupController@editForm'
        )
      );

      Route::post('/setups/edit/{id}',
        array(
          'as' => 'admin_setups_do_edit',
          'uses' => 'SetupController@editFormProcess'
        )
      );

      Route::get('/setups/create',
        array(
		      'as' => 'admin_setups_create_form',
		      'uses' => 'SetupController@createForm'
        )
      );

      Route::post('/setups/create',
        array(
          'as' => 'admin_setups_do_create_form',
          'uses' => 'SetupController@createFormProcess'
        )
      );


      //Products
      Route::get('/products/{page?}',
        array(
		      'as' => 'admin_product_list',
		      'uses' => 'ProductController@index'
	       )
      )->where('page', '[0-9]+');

      Route::get('/products/importer',
        array(
          'as' => 'admin_product_import',
		      'uses' => 'ProductController@import'
        )
      );

        Route::get('/products/importer/price',
            array(
                'as' => 'admin_product_price_import',
                'uses' => 'ProductController@importPrices'
            )
        );

      Route::get('/products/starter',
        array(
		      'as' => 'admin_product_import_starter',
		      'uses' => 'ProductController@importStarter'
        )
      );

      Route::get('/products/export',
        array(
          'as' => 'admin_product_export',
		      'uses' => 'ProductController@export'
        )
      );

      Route::post('/products/upload',
        array(
		      'as' => 'admin_products_upload',
		      'uses' => 'ProductController@upload'
        )
      );

      Route::post('/products/import/{importType}',
        array(
		      'as' => 'admin_products_do_import',
		      'uses' => 'ProductController@doImport'
        )
      );

      Route::post('/products/update_routes',
        array(
          'as' => 'admin_products_update_routes',
		      'uses' => 'ProductController@updateRoutesAction'
        )
      );

      Route::post('/products/update_categories_routes',
        array(
          'as' => 'admin_categories_update_routes',
		      'uses' => 'ProductController@updateCategoriesRoutesAction'
	      )
      );

      Route::post('/products/update_category_tree',
        array(
		      'as' => 'admin_categories_update_tree',
		      'uses' => 'CategoryController@updateCategoryTreeAction'
        )
      );

      Route::post('/products/update_category_tree_cache',
        array(
		      'as' => 'admin_categories_update_tree_cache',
		      'uses' => 'CategoryController@updateCategoryTreeCacheAction'
	     )
      );

      Route::get('/products/test-import',
        array(
		      'as' => 'admin_products_test_import',
		      'uses' => 'ProductController@iframeTest'
        )
      );

      Route::get('/products/generate',
        array(
		      'as' => 'admin_products_test_generate',
		      'uses' => 'ProductController@generate'
        )
      );

      Route::get('/products/edit/{id}',
        array(
		      'as' => 'admin_products_edit',
		      'uses' => 'ProductController@editForm'
	     )
      );

      Route::post('/products/edit/{id}',
        array(
		      'as' => 'admin_products_do_edit',
		      'uses' => 'ProductController@editFormProcess'
        )
      );

      Route::post('/products/batch',
        array(
		      'as' => 'admin_products_batch',
		      'uses' => 'ProductController@batchAction'
        )
      );

      Route::get('/products/integrity-check',
        [
		      'as' => 'products_integrity_check',
		      'uses' => 'ProductController@integrityCheckAction'
	      ]
      );

        //brands
        Route::get('/brands',[
            'uses' => 'BrandsController@index',
            'as' => 'admin.brands.index'
        ]);

        Route::post('/brands/modify-visible',[
            'uses' => 'BrandsController@modifyVisibility',
            'as' => 'admin.brands.modify.visibility'
        ]);

        Route::post('brands/destroy',[
            'uses' => 'BrandsController@destroy',
            'as' => 'admin.brands.destroy'
        ]);

        Route::get('brands/export',[
            'uses' => 'BrandsController@export',
            'as' => 'admin.brands.export'
        ]);

        Route::get('brands/export-blank',[
            'uses' => 'BrandsController@exportBlankTemplate',
            'as' => 'admin.brands.export.blank'
        ]);

        Route::get('brands/importer',[
            'uses' => 'BrandsController@importView',
            'as' => 'admin.brands.importView'
        ]);

        Route::post('/brands/upload',
            array(
                'as' => 'brands.admin.upload',
                'uses' => 'BrandsController@upload'
            )
        );

        Route::post('/brands/import',
            array(
                'as' => 'brands.admin.do.import',
                'uses' => 'BrandsController@doImport'
            )
        );

        Route::post('/brands/{page?}',[

            'uses' => 'BrandsController@indexDataTable',
            'as' => 'admin.brands.index.dataTable'
        ]);

        Route::get('/brands/edit/{id}',
            array(
                'as' => 'admin.brands.edit',
                'uses' => 'BrandsController@edit'
            )
        );

        Route::post('/brands/edit/{id}',
            array(
                'as' => 'admin.brands.update',
                'uses' => 'BrandsController@update'
            )
        );


        //stores
        Route::get('/stores',[
            'uses' => 'StoresController@index',
            'as' => 'admin.stores.index'
        ]);

        Route::post('/stores/modify-visible',[
            'uses' => 'StoresController@modifyVisibility',
            'as' => 'admin.stores.modify.visibility'
        ]);

        Route::post('stores/destroy',[
            'uses' => 'StoresController@destroy',
            'as' => 'admin.stores.destroy'
        ]);

        Route::get('stores/export',[
            'uses' => 'StoresController@export',
            'as' => 'admin.stores.export'
        ]);

        Route::get('stores/export-blank',[
            'uses' => 'StoresController@exportBlankTemplate',
            'as' => 'admin.stores.export.blank'
        ]);

        Route::get('stores/importer',[
            'uses' => 'StoresController@importView',
            'as' => 'admin.stores.importView'
        ]);

        Route::post('/stores/upload',
            array(
                'as' => 'stores.admin.upload',
                'uses' => 'StoresController@upload'
            )
        );

        Route::post('/stores/import',
            array(
                'as' => 'stores.admin.do.import',
                'uses' => 'StoresController@doImport'
            )
        );

        Route::post('/stores/{page?}',[

            'uses' => 'StoresController@indexDataTable',
            'as' => 'admin.stores.index.dataTable'
        ]);

        Route::get('/stores/edit/{id}',
            array(
                'as' => 'admin.stores.edit',
                'uses' => 'StoresController@edit'
            )
        );

        Route::post('/stores/edit/{id}',
            array(
                'as' => 'admin.stores.update',
                'uses' => 'StoresController@update'
            )
        );


        //Translations
      Route::get('/translations/{page?}',
        array(
		      'as' => 'admin_translations_list',
		      'uses' => 'TranslationController@index'
        )
      )->where('page', '[0-9]+');

      Route::get('/translations/importer',
        array(
		      'as' => 'admin_translations_import',
		      'uses' => 'TranslationController@import'
	     )
      );
      Route::get('/translations/export',
        array(
		      'as' => 'admin_translation_export',
		      'uses' => 'TranslationController@export'
	     )
      );
      Route::get('/translations/edit/{id}',
        array(
          'as' => 'admin_translations_edit',
          'uses' => 'TranslationController@editForm'
        )
      );

      Route::post('/translations/edit/{id}',
        array(
          'as' => 'admin_translations_do_edit',
          'uses' => 'TranslationController@editFormProcess'
        )
      );
      Route::post('/translations/upload',
        array(
		      'as' => 'admin_translations_upload',
		      'uses' => 'TranslationController@upload'
	     )
      );

      Route::post('/translations/import',
        array(
          'as' => 'admin_translations_do_import',
		      'uses' => 'TranslationController@doImport'
	      )
      );

      //Categories
      Route::get('/categories/{page?}',
        array(
		      'as' => 'admin_categories_list',
		      'uses' => 'CategoryController@index'
        )
      )->where('page', '[0-9]+');

      Route::get('/categories/importer',
        array(
		      'as' => 'admin_categories_import',
		      'uses' => 'CategoryController@import'
	     )
      );

      Route::get('/categories/starter',
        array(
		      'as' => 'admin_category_import_starter',
		      'uses' => 'CategoryController@importStarter'
	      )
      );

      Route::get('/categories/export',
        array(
		      'as' => 'admin_category_export',
		      'uses' => 'CategoryController@export'
	     )
      );

      Route::post('/categories/upload',
        array(
		      'as' => 'admin_categories_upload',
		      'uses' => 'CategoryController@upload'
	     )
      );

      Route::post('/categories/import',
        array(
          'as' => 'admin_categories_do_import',
		      'uses' => 'CategoryController@doImport'
	      )
      );

      Route::get('/categories/test-import',
        array(
		      'as' => 'admin_categories_test_import',
		      'uses' => 'CategoryController@iframeTest'
        )
      );

      Route::get('/categories/edit/{id}',
        array(
		      'as' => 'admin_categories_edit',
		      'uses' => 'CategoryController@editForm'
        )
      );

      Route::post('/categories/edit/{id}',
        array(
		      'as' => 'admin_categories_do_edit',
		      'uses' => 'CategoryController@editFormProcess'
        )
      );

      Route::post('/categories/batch',
        array(
          'as' => 'admin_categories_batch',
		      'uses' => 'CategoryController@batchAction'
        )
      );


      // Imports & exports
      Route::get('/imports/{page?}',
        array(
          'as' => 'admin_imports_done',
		      'uses' => 'AdminController@importsAction'
        )
      )->where('page', '[0-9]+');

      Route::get('/exports/{page?}',
        array(
		      'as' => 'admin_exports_done',
		      'uses' => 'AdminController@exportsAction'
        )
      )->where('page', '[0-9]+');

      Route::get('/feed-download/{type}/{feed}',
        array(
		      'as' => 'feed_url',
		      'uses' => 'AdminController@feedAction'
        )
      );

      Route::get('/import-log/{id}',
        [
          'uses' => 'AdminController@importLogFileAction',
		      'as' => 'admin_import_log_file'
        ]
      );


      // Optimize
      Route::get('/optimize',
        [
          'uses' => 'AdminController@optimizeAction',
		      'as' => 'optimize'
        ]
      );

      Route::post('/optimize',
        [
		      'uses' => 'AdminController@postOptimizeAction',
		      'as' => 'post_optimize'
        ]
      );


      // Sorting
      Route::get('/categories/sorting',
        [
		      'as' => 'admin_category_sorting',
		      'uses' => 'CategoryController@sortingAction'
        ]
      );

      Route::post('/categories/update-sorting',
        [
          'as' => 'post_update_sorting',
		      'uses' => 'CategoryController@updateSortingAction'
        ]
      );


	    //Search engine
      Route::get('/search-engine',
        array(
          'as' => 'search_engine_index',
		      'uses' => 'SearchEngineController@indexAction'
        )
      );

      Route::get('/search-engine/searches/{page?}',
        array(
          'as' => 'search_engine_latest',
          'uses' => 'SearchEngineController@latestSearches'
        )
      )->where('page', '[0-9]+');

      Route::match(['post', 'get'],'/search-engine-refresh',
        array(
          'as' => 'search_engine_refresh',
		      'uses' => 'SearchEngineController@updateSearchIndex'
        )
      );

      Route::get('/search-engine-empty',
        array(
          'as' => 'search_engine_empty',
		      'uses' => 'SearchEngineController@emptySearchIndex'
        )
      );


      // Miscelaneous
      Route::get('/logs',
        array(
          'as' => 'admin_show_logs',
		      'uses' => 'SettingsController@logsAction'
        )
      );

      Route::get('/settings',
        array(
		      'as' => 'admin_settings',
		      'uses' => 'SettingsController@indexAction'
        )
      );

      Route::get('/color-codes',
        [
          'as' => 'admin_color_codes',
		      'uses' => 'SettingsController@colorsAction'
        ]
      );

      Route::post('/color-codes',
        [
		      'as' => 'admin_post_color_codes',
		      'uses' => 'SettingsController@postColorsAction'
        ]
      );

      Route::post('/set-multicolor',
        [
		      'as' => 'admin_set_multicolor',
		      'uses' => 'SettingsController@setMulticolorAction'
        ]
      );

      Route::post('/settings',
        array(
		      'as' => 'admin_settings_post',
		      'uses' => 'SettingsController@settingsPost'
        )
      );

      Route::get('/settings/emails/{template?}',
        array(
		      'as' => 'email_templates_config',
		      'uses' => 'SettingsController@emailTemplatesAction'
        )
      );

      Route::post('/settings/emails/{template?}',
        array(
		      'as' => 'post_email_templates_config',
		      'uses' => 'SettingsController@postEmailTemplatesAction'
        )
      );

      Route::post('/setting/test-email',
        array(
          'as' => 'admin_test_mail',
          'uses' => 'SettingsController@postTestEmail'
        )
      );

      Route::post('/global-action',
        [
		      'admin_global-action',
		      'uses' => 'AdminController@globalAction'
        ]
      );

    }
  );
