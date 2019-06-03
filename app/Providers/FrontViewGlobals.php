<?php namespace ProjectCarrasco\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use ProjectCarrasco\Category;
use ProjectCarrasco\MenuConfiguration;
use ProjectCarrasco\Services\MainService;
use ProjectCarrasco\Theme;
use ProjectCarrasco\Setup;

/**
 * Class FrontViewGlobals
 * @package ProjectCarrasco\Providers
 * @author Andrés Solenzal asolenzal@gmail.com
 * OPTIMIZED
 */

class FrontViewGlobals extends ServiceProvider {

	protected $app;

	function __construct(Application $application){
		$this->app = $application;
	}

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot() {
    \View::composer(
      array('master', 'main/index', 'main/new_homepage', 'main/category'), function(View $v) {
        
        $localeArray = get_current_prefixes();
        if ( !isset($localeArray[0]) ) {
          $localeArray[0] = '';
        }
        if ( !isset($localeArray[1]) ) {
          $localeArray[1] = '';
        }
        $currentLocale = get_current_locate($localeArray[0],$localeArray[1]);
        $currentLocale = str_replace('/','',$currentLocale);
        $pieces = explode("-", $currentLocale);
        $country = strtolower($pieces[0]);  
        $language = strtolower($pieces[1]);
  
        $menu_definition = app('MainService')->getMenuConfigurationArray();
        $categories_tree = null;

        if (\Cache::has('categories_tree'.'_'.$country.'_'.$language)) {
          $categories_tree = \Cache::get('categories_tree'.'_'.$country.'_'.$language);
        } 
        else {
          $categories_tree = Category::updateFullCategoryHierarchy();
				  \Cache::forever('categories_tree'.'_'.$country.'_'.$language, $categories_tree);
        }
        if (\Cache::has('theme_definition'.'_'.$country.'_'.$language)) {
          $theme_definition = \Cache::get('theme_definition'.'_'.$country.'_'.$language);
        } 
        else {
          $theme = Theme::where('country', $country)->where('language', $language)->orderBy('created_at','DESC')->take(1)->first();
          if ($theme) {
            $theme_definition = json_decode($theme->data, true);
            \Cache::forever('theme_definition'.'_'.$country.'_'.$language, $theme_definition);
          } 
          else {
            $theme_definition = [];
          }
        }
        $setups = Setup::where(
          function ($query) {
            $query->where('country_abre', '!=', get_current_country())
                  ->orWhere('language_abre', '!=', get_current_language());
          }
        )->orderBy('country','ASC')->get();
        
        
        
        $v->with(array('menu_definition' => $menu_definition, 'categories_tree' => $categories_tree, 'theme_definition' => $theme_definition, 'setups' => $setups));
		  }
    );
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register() {
	
  }

}
