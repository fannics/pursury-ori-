<?php namespace ProjectCarrasco\Http\Controllers;

use ProjectCarrasco\Category;
use ProjectCarrasco\Helpers\SmartImageResizer;
use ProjectCarrasco\Http\Requests;
use ProjectCarrasco\Http\Controllers\Controller;

use Illuminate\Http\Request;
use ProjectCarrasco\Theme;

class HomepageController extends Controller {

	public function editAction(){

		return view('admin/homepage_edit');

	}

	public function definitionAction(Request $request){
  
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
    $theme = Theme::where('country', $country)->where('language', $language)->orderBy('created_at','DESC')->take(1)->first();
    if ($theme) {
			return response()->json(['status' => 'success', 'data' => json_decode($theme->data, true), 'id' => $theme->id]);
		} 
    else {

			$definition = [
				'theme' => null,
				'navigation' => [
					'items' => [

					]
				],
				'home_top' => [
					'title' => '',
					'buttons' => []
				],
				'home_bottom' => [
					'title' => [],
					'buttons' => [],
					'navigation' => [
						'items' => [

						]
					]
				],
				'footer' => [
				]
			];

			$theme = new Theme();
			$theme->data = json_encode($definition);
			$theme->status = 'current';
      $theme->country = $country;
      $theme->language = $language;
			$theme->save();

			return response()->json(['status' => 'success', 'data' => json_decode($theme->data), 'id' => $theme->id]);

		}
	}

	public function handleUpdateAction(Request $request){

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
    
    $theme = Theme::find($request->input('theme_id'));
    
		if ($theme){
			$theme->data = json_encode($request->input('data'));
      $theme->country = $country;
      $theme->language = $language;
			$theme->save();

			if (\Cache::has('theme_definition'.'_'.$country.'_'.$language)){
				\Cache::forget('theme_definition'.'_'.$country.'_'.$language);
			}

			\Cache::forever('theme_definition'.'_'.$country.'_'.$language, json_decode($theme->data, true));
      
			return response()->json(['status' => 'success', 'data' => json_decode($theme->data, true)]);
		}

		return response()->json(['status' => 'fail', 'message' => trans('json.invalidTheme')]);
	}

	public function validateUrlAction(Request $request){

		$ch = curl_init($request->input('url'));

		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$body = curl_exec($ch);

		$info = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

		if (in_array($info, [200, 301, 302])){
			return response()->json(['status' => 'success']);
		} else {
			return response()->json(['status' => 'fail']);
		}

	}

	public function getCategoriesForNavigationAction(){

		$categories = Category::visible()->where('country',get_current_country())->where('language',get_current_language())->get(['id', 'title', 'url_key']);
		$results = [];

		foreach($categories as $cat){
			$results[] = [
				'text' => $cat->title,
				'url' => prefixed_route($cat->url_key),
			];
		};

		return response()->json(['status' => 'success', 'categories' => $results]);

	}

	private function uploadAndResizeImage($file, $width, $height, $image_location){

		if (!in_array(strtolower($file->getClientOriginalExtension()), ['jpeg', 'jpg','png'])) {
			return response()->json(array(
				'status' => 'fail',
				'message' => trans('json.mustSelectValidImage')
			));
		}

		$directory = app_path('..'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'uploads').DIRECTORY_SEPARATOR;

		$name_without_extension = str_replace('.' . $file->getClientOriginalExtension(), '', $file->getClientOriginalName());

		$new_filename = md5($name_without_extension.time()).'.'.$file->getClientOriginalExtension();

		try{

			$file->move($directory, $new_filename);

			$result = SmartImageResizer::smart_resize_image(
				$directory.$new_filename,
				1680,
				700,
				true,
				'file'
			);

			\Settings::set('app.'.$image_location, '/uploads/'.$new_filename);

		} catch (\Exception $e){

			\Log::error($e->getMessage());

			return response()->json([
				'status' => 'fail',
				'message' => trans('json.thereWasError')
			]);
		}



		return response()->json([
			'status' => 'fail',
			'message' => trans('json.fileUploadedSuccess')
		]);

	}


	public function uploadHomeBackgroundAction(Request $request){

		if (!$request->hasFile('home_background')){
			return response()->json([
				'status' => 'fail',
				'message' => trans('json.mustSelectFileUpload')
			]);
		}

		$file = $request->file('home_background');

		return $this->uploadAndResizeImage($file, 1680, 700, 'homepage_background');

	}

	public function uploadLogoAction(Request $request){

		if (!$request->hasFile('logo_upload')){

			return response()->json([
				'status' => 'fail',
				'message' => trans('json.mustSelectFileUpload')
			]);

		}

		$file = $request->file('logo_upload');

		return $this->uploadAndResizeImage($file, 250, 50, 'site_logo');
	}

	public function uploadSmallLogoAction(Request $request){

		if (!$request->hasFile('small_logo_upload')){

			return response()->json([
				'status' => 'fail',
				'message' => trans('json.mustSelectFileUpload')
			]);

		}

		$file = $request->file('small_logo_upload');

		return $this->uploadAndResizeImage($file, 100, 70, 'footer_logo');
	}



}
