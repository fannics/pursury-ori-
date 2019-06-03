var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {

    mix.copy(
        'resources/assets/images',
        'public/images'
    );

    mix.copy(
        'resources/assets/fonts',
        'public/dist/fonts'
    );

    mix.styles([
        '/bower_components/bootstrap/dist/css/bootstrap.min.css',
        '/bower_components/animate.css/animate.min.css',
        '/bower_components/webui-popover/dist/jquery.webui-popover.min.css',
        '/bower_components/iCheck/skins/square/blue.css',
        '/bower_components/font-awesome/css/font-awesome.min.css',
        '/bower_components/EasyAutocomplete/dist/easy-autocomplete.min.css',
        '/bower_components/jQuery.mmenu/dist/core/css/jquery.mmenu.all.css',
        '/bower_components/jquery.scrollbar/jquery.scrollbar.css',
        '/bower_components/nouislider/distribute/nouislider.min.css'
    ],
        'public/dist/css/front_vendors.css',
        'resources/assets'
    );

    mix.styles([
        '/css/appfront/stylesheets/screen.css'
    ],
        'public/dist/css/front.css',
        'resources/assets'
    );

    mix.scripts([
        '/bower_components/jquery/dist/jquery.min.js',
        '/bower_components/bootstrap/dist/js/bootstrap.min.js',
        '/bower_components/df-visible/jquery.visible.min.js',
        '/bower_components/webui-popover/dist/jquery.webui-popover.min.js',
        '/bower_components/iCheck/icheck.min.js',
        '/bower_components/bootbox/bootbox.js',
        '/bower_components/jquery-validation/dist/jquery.validate.min.js',
        '/bower_components/jquery-validation/dist/additional-methods.min.js',
        '/bower_components/jquery-validation/src/localization/messages_es.js',
        '/bower_components/EasyAutocomplete/dist/jquery.easy-autocomplete.min.js',
        '/bower_components/jQuery.mmenu/dist/core/js/jquery.mmenu.min.all.js',
        '/bower_components/SnsShare/jquery.snsShare.js',
        '/bower_components/jquery.scrollbar/jquery.scrollbar.min.js',
        '/bower_components/nouislider/distribute/nouislider.min.js',
        '/bower_components/animatedscrolljs/jquery.animatedscroll.js'
    ],
        'public/dist/js/front_vendors.js',
        'resources/assets'
    );

    mix.scripts([
        '/js/custom_autocomplete/custom_autocomplete.js',
        // '/js/scrollableNav.js',
        '/js/seeMoreFilters.js',
        '/js/c.js',
        '/js/a.js',
        '/js/pages/login.js',
        '/js/pages/register.js',
        '/js/pages/password.js',
        '/js/pages/change_email.js',
        '/js/pages/change_password.js',
        '/js/pages/activation.js',
        '/js/front.js',
        '/js/filters.js',
        '/js/productModal.js'
    ],
        'public/dist/js/front_app.js',
        'resources/assets'
    );

    mix.styles([
        '/bower_components/bootstrap/dist/css/bootstrap.min.css',
        '/bower_components/font-awesome/css/font-awesome.min.css',
        '/bower_components/iCheck/skins/square/blue.css',
        '/bower_components/select2/select2.css',
        '/bower_components/select2/select2-bootstrap.css',
        '/bower_components/blueimp-file-upload/css/jquery.fileupload.css',
        '/bower_components/DataTables/DataTables-1.10.12/css/jquery.dataTables.min.css',
        '/bower_components/DataTables/DataTables-1.10.12/css/dataTables.bootstrap.min.css',
        '/bower_components/DataTables/Buttons-1.2.2/css/buttons.dataTables.min.css',
        '/bower_components/DataTables/Buttons-1.2.2/css/buttons.bootstrap.min.css',
        '/bower_components/DataTables/Select-1.2.0/css/select.dataTables.min.css',
        '/bower_components/DataTables/Select-1.2.0/css/select.bootstrap.min.css',
        '/bower_components/jstree/dist/themes/default/style.min.css',
        '/bower_components/webui-popover/dist/jquery.webui-popover.min.css',
        ],
        'public/dist/css/admin_vendors.css',
        'resources/assets'
    );

    mix.styles([
        '/css/appback/stylesheets/screen.css',
        '/js/infiniteLoading/infiniteLoading.css'
    ],
        'public/dist/css/admin.css',
        'resources/assets'
    );

    mix.scripts([
        '/bower_components/jquery/dist/jquery.min.js',
        '/bower_components/bootstrap/dist/js/bootstrap.min.js',
        '/bower_components/iCheck/icheck.min.js',
        '/bower_components/df-visible/jquery.visible.min.js',
        '/bower_components/jquery-validation/dist/jquery.validate.min.js',
        '/bower_components/jquery-validation/dist/additional-methods.min.js',
        '/bower_components/jquery-validation/src/localization/messages_es.js',
        '/bower_components/bootbox/bootbox.js',
        '/bower_components/select2/select2.min.js',
        '/bower_components/blueimp-file-upload/js/jquery.iframe-transport.js',
        '/bower_components/blueimp-file-upload/js/vendor/jquery.ui.widget.js',
        '/bower_components/blueimp-file-upload/js/jquery.fileupload.js',
        '/bower_components/tinycolor/dist/tinycolor-min.js',
        '/bower_components/DataTables/DataTables-1.10.12/js/jquery.dataTables.min.js',
        '/bower_components/DataTables/DataTables-1.10.12/js/dataTables.bootstrap.min.js',
        '/bower_components/DataTables/Buttons-1.2.2/js/dataTables.buttons.js',
        '/bower_components/DataTables/Buttons-1.2.2/js/buttons.bootstrap.js',
        '/bower_components/DataTables/Select-1.2.0/js/dataTables.select.js',
        '/bower_components/jstree/dist/jstree.min.js',
        '/bower_components/webui-popover/dist/jquery.webui-popover.min.js',
        ],
        'public/dist/js/admin_vendors.js',
        'resources/assets'
    );

    mix.styles([
        '/bower_components/angular-color-picker/dist/angularjs-color-picker.min.css',
        '/bower_components/angular-color-picker/dist/themes/angularjs-color-picker-bootstrap.min.css'

        ],
        'public/dist/css/home_edit.css',
        'resources/assets'
    );

    mix.scripts([
        '/bower_components/angular/angular.js',
        '/bower_components/angular-bootstrap/ui-bootstrap.min.js',
        '/bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js',
        '/bower_components/angular-color-picker/dist/angularjs-color-picker.min.js'
        ],
        'public/dist/js/home_edit.js',
        'resources/assets'
    );

    mix.scripts([
        '/js/pages/home_edit_page.js',
        ],
        'public/dist/js/home_edit_page.js',
        'resources/assets'
    );

    mix.scripts([
        '/js/c.js',
        '/js/pages/admin_categories_form.js',
        '/js/pages/admin_products_form.js',
        '/js/pages/admin-email-templates.js',
        '/js/pages/dashboard.js',
        '/js/pages/menu_configuration.js',
        '/js/pages/admin_settings.js',
        '/js/pages/admin_logs.js',
        '/js/infiniteLoading/infiniteLoading.js',
        '/js/pages/product_import.js',
        '/js/pages/categories_import.js',
        '/js/ca.js'
        ],
        'public/dist/js/admin_app.js',
        'resources/assets'
    );

    mix.styles([
            '/css/installer/stylesheets/screen.css'
        ],
        'public/insassets/css/installer.css',
        'resources/assets'
    );

    mix.scripts([
            '/js/installer.js'
        ],
        'public/insassets/js/installer.js',
        'resources/assets'
    );

});
