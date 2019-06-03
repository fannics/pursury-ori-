<?php namespace ProjectCarrasco\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use ProjectCarrasco\ColorCodes;
use ProjectCarrasco\EmailTemplate;
use ProjectCarrasco\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use ProjectCarrasco\Http\Requests\SettingsFormRequest;
use ProjectCarrasco\ProductProperty;
use ProjectCarrasco\Settings;
use ProjectCarrasco\Translation;
use ProjectCarrasco\TranslationCatalog;
use Rap2hpoutre\LaravelLogViewer\LaravelLogViewer;

class SettingsController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function indexAction()
	{
		return view('admin/settings');
	}

	public function logsAction(Request $request){

		$log_type = null;

		$filename = base64_decode($request->input('l'));
		if ($request->input('dl')) {
			return Response::download(storage_path() . '/logs/' . base64_decode($request->input('dl')));
		} elseif ($request->has('del')) {
			File::delete(storage_path() . '/logs/' . base64_decode($request->input('del')));
			return Redirect::to($request->url());
		}

		if (strpos($filename, 'import_') !== false){

			if ($filename){

				$log_type = 'import_log';

				$logs = file_get_contents(storage_path('logs/'.$filename));

			}

		} else {

			if ($filename){
				LaravelLogViewer::setFile($filename);

				$log_type = 'regular_log';
			}

			$logs = LaravelLogViewer::all();
		}

		return view('admin/logs', array(
			'logs' => $logs,
			'files' => LaravelLogViewer::getFiles(true),
			'current_file' => LaravelLogViewer::getFileName(),
			'log_type' => $log_type
		));
	}

	public function settingsPost(SettingsFormRequest $request){

		\Settings::flush();

		\Settings::set('app.app_title', $request->input('app_title', settings('app.app_title')));

		\Settings::set('app.route_prefix', $request->input('route_prefix', settings('app.route_prefix')));


		\Settings::set('app.debug', $request->input('debug') == 'on' ? true : false);

		\Settings::set('app.elasticsearch_host', $request->input('elasticsearch_host', settings('app.elasticsearch_host')));
		\Settings::set('app.elasticsearch_index_name', $request->input('elasticsearch_index_name', settings('app.elasticsearch_index_name')));

		\Settings::set('app.image_processor', $request->input('image_processor', settings('app.image_processor')));

		\Settings::set('app.thumbor_address', $request->input('thumbor_address', settings('app.thumbor_address')));

		\Settings::set('app.thumbnail_size_for_tile', $request->input('thumbnail_size_for_tile_width').'x'.$request->input('thumbnail_size_for_tile_height'));
		\Settings::set('app.product_file_image_size', $request->input('product_file_image_size_width').'x'.$request->input('product_file_image_size_height'));

		\Settings::set('app.currency_name', $request->input('currency_name', settings('app.currency_name')));
		\Settings::set('app.currency_html_code', $request->input('currency_html_code') ? htmlentities($request->input('currency_html_code')) : settings('app.currency_html_code'));
		\Settings::set('app.currency_location', $request->input('currency_location', settings('app.currency_location')));

		\Settings::set('app.money_decimal_digits', $request->input('money_decimal_digits', settings('app.money_decimal_digits')));
		\Settings::set('app.money_decimal_separator', $request->input('money_decimal_separator', settings('app.money_decimal_separator')));
		\Settings::set('app.money_thousands_separator', $request->input('money_thousands_separator', settings('app.money_thousands_separator')));

//		\Settings::set('app.product_order_limit', $request->input('product_order_limit'), 20);

		return redirect(route('admin_settings'));
	}

	private function getEmailTemplateContent($template){

		$view_path = app_path('..'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'emails'.DIRECTORY_SEPARATOR);

		$view_path = $view_path.$template->view_name.'.blade.php';

		if (file_exists($view_path)){

			return file_get_contents($view_path);

		} else {
			return 'No se encuentra el fichero asociado a la plantilla '.$template->name;
		}
	}

	private function putEmailTemplateContent($template, $content){

		$view_path = app_path('..'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'emails'.DIRECTORY_SEPARATOR);

		$view_path = $view_path.$template->view_name.'.blade.php';

		if (file_exists($view_path)){

			return file_put_contents($view_path, $content);

		} else {
			return 'No se encuentra el fichero asociado a la plantilla '.$template->name;
		}
	}

	public function emailTemplatesAction(Request $request, $template = null){

		$master_template = EmailTemplate::query()->where('label', '=', 'master')->first();

		$other_templates = EmailTemplate::query()->where('label', '!=', 'master')->get();

		$template_content = '';

		if ($template != null){

			$template = EmailTemplate::query()->where('label', $template)->first();

			if ($template){

				$template_content = $this->getEmailTemplateContent($template);

			} else {
				abort(404);
			}

		} else {

			$template = $other_templates->first();

			$template_content = $this->getEmailTemplateContent($template);

		}

		return view('admin/email_templates', [
			'master' => $master_template,
			'templates' => $other_templates,
			'template_content' => $template_content,
			'template' => $template
		]);
	}

	public function postEmailTemplatesAction(Request $request){

		if ($request->input('template_id')){

			$template = EmailTemplate::find($request->input('template_id'));

			if ($template){

				$this->putEmailTemplateContent($template, $request->input('template_content'));

				return redirect(route('email_templates_config', ['template' => $template->label]));

			} else {
				abort(404);
			}
		} else {
			\Session::flash('error', trans('flash.error.no_template_selected') );

			return redirect()->back();
		}
	}

	public function postTestEmail(Request $request){

		$validator = \Validator::make(
			array(
				'email' => $request->input('email'),
				'template' => $request->input('email')
			),
			array(
				'email' => 'required|email',
				'template' => 'required'
			),
			array(
				'email.required' => 'Debe escribir el email',
				'email.email' => 'Debe escribir un email válido',
				'template.required' => 'Debe seleccionar la plantilla que desea utilizar'
			)
		);

		if ($validator->fails()) {
			$errors = $validator->errors();
			$messages = [];

			foreach($errors->getMessages() as $error) {
				$messages[] = array_pop($error);
			}

			return new JsonResponse(array('status' => 'not_valid', 'messages' => $messages));

		} 
    else {
			$template = null;

			try {
				$template = EmailTemplate::query()->where('label', $request->input('template'))->firstOrFail();
			} 
      catch (ModelNotFoundException $e) {
				return new JsonResponse(array('status' => 'not_valid', 'messages' => array('No se encuentra la plantilla seleccionada')));
			}
			
      $required_vars = explode(',', $template->available_variables);
			$associative = array_flip($required_vars);
			$associative = array_map(
        function($v) {
          return sha1(time());
        }, $associative
      );
			$view_name = 'emails/'.$template->view_name;
			$email = $request->input('email');
      
			\Mail::send($view_name, $associative, 
        function($message) use ($email) {
          $message->setTo($email);
          $message->setSubject( settings('app.app_title').' - '.trans('emails.test_email_subject') );
        }
      );

			return new JsonResponse(array('status' => 'success'));
		}
    
	}

	public function colorsAction(Request $request){

		$current_colors = ProductProperty::getDistinctColorsAndCodes();

		$incomplete_color_count = ColorCodes::getIncompleteCount();

		$complete_colors = ColorCodes::getCompleteColorsCount();

		return view('admin/colors', ['current_colors' => $current_colors, 'incomplete_color_count' => $incomplete_color_count, 'complete_color_count' => $complete_colors]);

	}

	public function postColorsAction(Request $request){

		$color_name = $request->input('color_name');

		$components = $request->input('components');

		$color = ColorCodes::where('color_name', $color_name)
			->first();

		if ($color){

			$color->color_code = implode('/', $components);

			$color->save();

		} else {
			$color = new ColorCodes();
      $sessionCountry = isset($_COOKIE['sessionCountry']) ? $_COOKIE['sessionCountry'] : '';
      $sessionLanguage = isset($_COOKIE['sessionLanguage']) ? $_COOKIE['sessionLanguage'] : '';
      $currentLocale = get_current_locate($sessionCountry,$sessionLanguage);
      $currentLocale = str_replace('/','',$currentLocale);
      $pieces = explode("-", $currentLocale);
      $color->country = strtolower($pieces[0]);
      $color->language = strtolower($pieces[1]);
			$color->color_name = $color_name;
			$color->color_code = implode('/', $components);
			$color->save();
		}

		$service = app('MainService');

		//apply color inheritance
		if (strpos($color_name, '/') !== false){
			//is a compound color
			$single_colors = explode('/', $color_name);

			$updated_desc = 0;

			if (count($single_colors) == count($components)){
				//all color parts are defined

				foreach($single_colors as $key=>$single_color){
					$updated_desc += $service->updateColorDescendents($single_color, $components[$key]);
				}

			}

			return response()->json(['status' => 'success', 'color' => $color->toArray(), 'needs_reload' => $updated_desc > 0 ? true : false]);

		} else {
			//is a single color

			$updated_desc = 0;

			$updated_desc += $service->updateColorDescendents($color_name, array_pop($components));

			return response()->json(['status' => 'success', 'color' => $color->toArray(), 'needs_reload' => $updated_desc > 0 ? true : false]);
		}

	}

	public function setMulticolorAction(Request $request){
		\Settings::flush();

		\Settings::set('app.multicolor_value', $request->input('multicolor', settings('app.multicolor_value')));

		return response()->json(['status' => 'success']);
	}

}
