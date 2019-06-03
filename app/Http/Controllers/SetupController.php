<?php namespace ProjectCarrasco\Http\Controllers;

use Illuminate\Support\Facades\View;
use ProjectCarrasco\Setup;
use ProjectCarrasco\Http\Requests;
use ProjectCarrasco\Http\Controllers\Controller;

use Waavi\Translation\Models\Language;

use Illuminate\Http\Request;
use ProjectCarrasco\Paginator\AppPaginator;
use Psy\Util\Json;
use Symfony\Component\HttpFoundation\JsonResponse;

class SetupController extends Controller {

	public function index(Request $request, $page = 1)
	{
  
		if ($request->isXmlHttpRequest()) {
      
			$pagination_fields = $this->getDataTablesInfoForQuery($request->input());

			$setups = Setup::paginateForAdmin($pagination_fields);

			$total_setups = Setup::getSetupsCount();

			return response()->json($this->convertPaginatorToDataTableInput($setups, $total_setups));
		}
    
		return view('setup/index');
    
	}
  
  public function editForm(Request $request, $id) {

		$setup = Setup::find($id);

		if (!$setup){
			\Session::flash('error', trans('flash.error.setup_not_found') );
			return redirect(route('admin_setups_list'));
		}
		return view('setup/form', array('setup' => $setup));

	}                                                                          
  
  public function editFormProcess(Requests\SetupFormRequest $request, $id){

		try{

			$setup = Setup::find($id);
      $current_locale = strtoupper($setup->country_abre).'-'.strtolower($setup->language_abre);                                   

      // BEGIN - Check for integrity
      if ( ( strtoupper($setup->country_abre) != strtoupper($request->input('setup_country_abre')) ) || ( strtolower($setup->language_abre) != strtolower($request->input('setup_language_abre')) ) ) {       
        $language = Language::where('locale', strtoupper($request->input('setup_country_abre')).'-'.strtolower($request->input('setup_language_abre')))->first();
        if (null !== $language) {
          \Session::flash('error', trans('flash.error.setup_same_abbreviations') );
          return \Redirect::back()->withInput(\Input::all());
        }
      }
      
      if ( ( strtoupper($setup->country) != strtoupper($request->input('setup_country')) ) || ( strtolower($setup->language) != strtolower($request->input('setup_language')) ) ) {       
        $language = Language::where('name', strtoupper($request->input('setup_country')).'-'.strtolower($request->input('setup_language')))->first();
        if (null !== $language) {
          \Session::flash('error', trans('flash.error.setup_same_countylanguage') );
          return \Redirect::back()->withInput(\Input::all());
        }
      }
      // END - Check for integrity

			$setup->country = $request->input('setup_country');
      $setup->country_abre = $request->input('setup_country_abre');
      $setup->language = $request->input('setup_language');
      if ($request->input('setup_default_language') == 1) { 
        Setup::where('country_abre', $request->input('setup_country_abre'))
                ->where('id', '!=', $id)
                ->update(['default_language' => 0]);
        $setup->default_language = 1;
      }
      else {
        $setup->default_language = 0;
      }
      $setup->language_abre = $request->input('setup_language_abre');
      $setup->currency = $request->input('setup_currency');
      $setup->currency_symbol = $request->input('setup_currency_symbol');
      $setup->before_after = $request->input('setup_before_after');
      $setup->currency_decimal = $request->input('setup_currency_decimal');
      if ($request->input('setup_default_setup') == 1) { 
        Setup::where('id', '!=', $id)
                ->update(['default_setup' => 0]);
        $setup->default_setup = 1;
      }
      else {
        $setup->default_setup = 0;
      }
			$setup->save();
      
      // BEGIN - Language update
      $language = Language::where('locale',$current_locale)->first();
      $language->locale = strtoupper( $setup->country_abre ).'-'.strtolower( $setup->language_abre ); 
      $language->name = strtoupper( $setup->country ).'-'.strtolower( $setup->language );
      $language->save();
      // END - Language update

			\Session::flash('success', trans('flash.success.operation_done_successfully') );

			return redirect(route('admin_setups_list'));

		} 
    catch (\Exception $e){
      throw $e;
		}
	}
  
  public function createForm(Request $request) {

		return view('setup/create');

	}

  public function createFormProcess(Requests\SetupFormRequest $request){
                                    
		try {

      // BEGIN - Check for integrity
      $language = Language::where('locale', strtoupper($request->input('setup_country_abre')).'-'.strtolower($request->input('setup_language_abre')))->first();
      if (null !== $language) {
         \Session::flash('error', trans('flash.error.setup_same_countylanguage2') );
         return \Redirect::back()->withInput(\Input::all());
      }

      $language = Language::where('name', strtoupper($request->input('setup_country')).'-'.strtolower($request->input('setup_language')))->first();
      if (null !== $language) {
        \Session::flash('error', trans('flash.error.setup_same_countylanguage3') );
        return \Redirect::back()->withInput(\Input::all());
      }
      // END - Check for integrity

      $setup = new Setup;
      $setup->country = $request->input('setup_country');
      $setup->country_abre = $request->input('setup_country_abre');      
      $setup->language = $request->input('setup_language');      
      if ($request->input('setup_default_language') == 1) { 
        Setup::where('country_abre', $request->input('setup_country_abre'))
                ->update(['default_language' => 0]);
        $setup->default_language = 1;
      }
      else {
        $setup->default_language = 0;
      }
      $setup->language_abre = $request->input('setup_language_abre');
      $setup->currency = $request->input('setup_currency');
      $setup->currency_symbol = $request->input('setup_currency_symbol');
      $setup->before_after = $request->input('setup_before_after');
      $setup->currency_decimal = $request->input('setup_currency_decimal');
      if ($request->input('setup_default_setup') == 1) { 
        Setup::where('default_setup', 1)
                ->update(['default_setup' => 0]);
        $setup->default_setup = 1;
      }
      else {
        $setup->default_setup = 0;
      }
      $setup->save();

      // BEGIN - Language creation
      $language = new Language;
      $language->locale = strtoupper( $setup->country_abre ).'-'.strtolower( $setup->language_abre ); 
      $language->name = strtoupper( $setup->country ).'-'.strtolower( $setup->language );
      $language->save();
      // END - Language creation

			\Session::flash('success', trans('flash.success.operation_done_successfully') );

			return redirect(route('admin_setups_list'));

		} 
    catch (\Exception $e) {
      throw $e;
		}
	}

}