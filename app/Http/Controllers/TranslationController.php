<?php namespace ProjectCarrasco\Http\Controllers;

use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\LexerConfig;
use Illuminate\Support\Facades\View;
use ProjectCarrasco\Translation;
use ProjectCarrasco\Export;
use ProjectCarrasco\Http\Requests;
use ProjectCarrasco\Http\Controllers\Controller;

use Illuminate\Http\Request;
use ProjectCarrasco\Import;
use ProjectCarrasco\Paginator\AppPaginator;
use Psy\Util\Json;
use Symfony\Component\HttpFoundation\JsonResponse;

class TranslationController extends Controller {

	public function index(Request $request, $page = 1)
	{

		if ($request->isXmlHttpRequest()) {
			$pagination_fields = $this->getDataTablesInfoForQuery($request->input());
			$translations = Translation::paginateForAdmin($pagination_fields);
			$total_translations = Translation::getTranslationsCount();
			return response()->json($this->convertPaginatorToDataTableInput($translations, $total_translations));
		}

		return view('translation/index');
	}

	public function import()
	{
		return view('translation/importer');
	}
  
  public function editForm(Request $request, $id) {

		$translation = Translation::find($id);

		if (!$translation){
			\Session::flash('error', trans('flash.error.translation_not_found') );
			return redirect(route('admin_translations_list'));
		}
		return view('translation/form', array('translation' => $translation));

	}                                                                          
  
  public function editFormProcess(Requests\TranslationFormRequest $request, $id){

		try{

			$translation = Translation::find($id);

			$translation->group = $request->input('translation_group');
      $translation->item = $request->input('translation_item');
      $translation->text = $request->input('translation_text');
			$translation->save();
      
			\Session::flash('success', trans('flash.success.operation_done_successfully') );

			return redirect(route('admin_translations_list'));

		} 
    catch (\Exception $e){
      throw $e;
		}
	}
  
	public function export(){

		$filename = 'Translation_Template - '.date('d-m-Y H:i:s', time()). '.csv';

		$export = new Export();
		$export->user_id = \Auth::user()->id;
		$export->filename = $filename;
		$export->type = 'translations';

		$export->save();

		$disk = \Storage::disk('local');

		if (!$disk->exists('exports/translations')){
			$disk->makeDirectory('exports/translations');
		}

		$handle = fopen(storage_path('app/exports/translations/').$filename, 'w');

		$required_fields = array(
			'Group',
			'Item',
			'Text'
		);

		set_time_limit(0);

		fputcsv($handle, $required_fields, ',');

		$translations = Translation::forExport();

		foreach($translations as $translation){

			$resultArray = json_decode(json_encode($translation), true);

			$resultArray = array_map(function($value) { return utf8_decode($value); }, $resultArray);

			$resultArray = array_map(function($value) {
				return preg_replace( "/\r|\n/", "", $value );
			}, $resultArray);
			fputcsv($handle, $resultArray, ',');
		}

		fclose($handle);

		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="'.$filename.'";');
		header("Cache-Control: no-store, no-cache");

		readfile(storage_path('app/exports/translations/').$filename);

	}
  
  public function upload(Request $request)
	{
		//validate the file
		if (!$request->hasFile('file_to_import')){
			return new JsonResponse(array(
				'status' => 'fail',
				'message' => trans('json.needSelectFile')
			));
		}

		$file = $request->file('file_to_import');

		if (strtolower($file->getClientOriginalExtension()) != 'csv') {
			return new JsonResponse(array(
				'status' => 'fail',
				'message' => trans('json.needValidCSV')
			));
		}

		$disk = \Storage::disk('local');

		if (!$disk->exists('feeds/translations')){
			$disk->makeDirectory('feeds/translations');
		}

		//the filename will renamed to filename - upload date in the format Y-m-d H:i:s and the file extension

		$uploaded_date = new \DateTime('now', new \DateTimeZone('UTC'));
		$uploaded_date = $uploaded_date->format('Y-m-d H:i:s');

		$name_without_extension = str_replace('.'.$file->getClientOriginalExtension(), '' ,$file->getClientOriginalName());

		$new_filename = $name_without_extension.' - '.$uploaded_date.'.'.$file->getClientOriginalExtension();

		$directory = storage_path('app/feeds/translations').DIRECTORY_SEPARATOR;

		$file->move(storage_path('app/feeds/translations').DIRECTORY_SEPARATOR, $new_filename);

		return new JsonResponse(array(
			'status' => 'success',
			'filename' => $new_filename,
		));
	}
  
	public function doImport(Request $request) {
    set_time_limit(0);
		$file_path = storage_path('app/feeds/translations').DIRECTORY_SEPARATOR.urldecode($request->input('file'));
    if (!file_exists($file_path)){
      return new JsonResponse([
				'status' => 'fail',
				'message' => trans('json.errorOpenFile')
			]);
		}
		$config = new LexerConfig();
		$config->setToCharset('UTF-8');
		$lexer = new Lexer($config);
		$log_file_name = 'import_' . date('Y-m-d-H:i:s') . '.log';
		$log_file_handle = fopen(storage_path('logs/') . $log_file_name, 'w');
		$totals = [
			'added' => [
				'count' => 0,
				'done' => 0,
			],
			'updated' => [
				'count' => 0,
				'done' => 0
			]
		];
		$interpreter = new Interpreter();
		$interpreter->unstrict();
		$first_line = true;
		$fields = null;
		$file_errors = false;
		$error_on_row = false;
		$deleted_rows = array();
		$interpreter->addObserver(function(array $row) use (&$first_line, &$fields, &$deleted_rows, $log_file_handle, &$totals, &$file_errors, $error_on_row) {
      $sessionCountry = isset($_COOKIE['sessionCountry']) ? $_COOKIE['sessionCountry'] : '';
      $sessionLanguage = isset($_COOKIE['sessionLanguage']) ? $_COOKIE['sessionLanguage'] : '';
      $currentLocale = get_current_locate($sessionCountry,$sessionLanguage);
      $currentLocale = str_replace('/','',$currentLocale);
      if ($first_line == true){
        $first_line = false;
        $fields = $row;
        //validate the fields
        $missing_fields = $this->validate_fields_array($fields);
        if (count($missing_fields) > 0) {
          $missing_fields_error = '';
          foreach ($missing_fields as $field) {
            $missing_fields_error .= 'Missing field ' . $field . '<br/>';
				  }
          return new JsonResponse([
            'status' => 'fail',
            'message' => $missing_fields_error
          ]);
			 }
      } 
      else {
        try {
          $value = array_combine($fields, $row);
			  } 
        catch (\Exception $e) {
          $error_on_row = true;
        }
        if ($error_on_row) {
          $error_on_row = false;
          fwrite($log_file_handle, 'Importing error: ' . $row . "\r\n");
          $file_errors = true;
        } 
        else {                               
            $found = \DB::table('translator_translations')->select()->where('group', '=', $value['Group'])->where('item', '=', $value['Item'])->where('locale','=',$currentLocale)->get();
            if ($found != null) {
              try {
                $totals['updated']['count']++;                                
                \DB::beginTransaction();
                \DB::table('translator_translations')->where('group', '=', $value['Group'])->where('item', '=', $value['Item'])->where('locale','=',$currentLocale)->update(array(
								  'text' => utf8_encode($value['Text'])
                ));
                \DB::commit();
                $totals['updated']['done']++;
						  } 
              catch (\Exception $e){
                \DB::rollBack();
                $file_errors = true;
                fwrite($log_file_handle, 'Error when updating ' . $value['Group'].'.'.$value['Item'] . ' Details: ' . $e->getMessage());
						  }
            } 
            else {
              try {
                $totals['added']['count']++;
                \DB::beginTransaction();
  						  \DB::table('translator_translations')->insertGetId(array(
                	'locale' => $currentLocale,
                  'namespace' => '*',
                  'group' => $value['Group'],
                  'item' => $value['Item'],
                  'text' => utf8_encode($value['Text']),
                  'unstable' => 0,
                  'locked' => 0,
							   ));
                \DB::commit();
                $totals['added']['done']++;
						  } 
              catch(\Exception $e){
                \DB::rollBack();
                fwrite($log_file_handle, 'Error when creating ' . $value['Group'].'.'.$value['Item'] . ' Details: ' . $e->getMessage());
                $file_errors = true;
						  }
            }
        }
		  }
    });
		$lexer->parse($file_path, $interpreter);
		$import = new Import();
		$import->user_id = \Auth::getUser()->id;
		$import->type = 'translations';
		$import->filename = $request->input('file');
		$import->log_file = $log_file_name;
		$import->totals = serialize($totals);
		$import->save();
		if ($file_errors) {
      return new JsonResponse([
        'status' => 'success',
				'message' => trans('json.importedTranslationsError'),
				'results' => \View::make('translation/import_results', ['import' => $import, 'totals' => $totals])->render()
			]);
		} 
    else {
      return new JsonResponse([
				'status' => 'success',
				'message' => trans('json.importedTranslationsSuccess'),
				'results' => \View::make('translation/import_results', ['import' => $import, 'totals' => $totals])->render()
			]);
    }
	}

	private function validate_fields_array($fields) {
		$required_fields = array(
			'Group',
			'Item',
			'Text',
		);
		return array_diff($required_fields, $fields);
	}

}