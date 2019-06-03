<?php namespace ProjectCarrasco\Http\Controllers;

use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\LexerConfig;
use Illuminate\Support\Facades\View;
use ProjectCarrasco\Category;
use ProjectCarrasco\Export;
use ProjectCarrasco\Http\Requests;
use ProjectCarrasco\Http\Controllers\Controller;

use Illuminate\Http\Request;
use ProjectCarrasco\Import;
use ProjectCarrasco\Paginator\AppPaginator;
use Psy\Util\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use ProjectCarrasco\Helpers\Uploader;
use Excel;

class CategoryController extends Controller {
    private $required_fields = array(
        'Category_ID',
        'Category_Title',
        'Categories',
        'Category_Short_Description',
        'Category_url_key',
        'Category_Is_Visible',
        'Category_Meta_Title',
        'Category_Meta_Description',
        'Category_Meta_Noindex',
        'Category_Delete',
        'Category_Filters',
        'Category_Default_Sorting',
        'Category_Parent',
        'Category_Reference',
        'Category_Img',
        'Category_Img_Thumbnail',
        'Category_Img_Alt',
        'Category_Description',
        'Category_Language',
        'Category_Country'
    );
    private $mapedKeys = [
        'category_id' =>  'Category_ID',
        'categories' =>  'Categories',
        'category_short_description' =>  'Category_Short_Description',
        'category_title' =>  'Category_Title',
        'category_url_key'=>  'Category_url_key',
        'category_is_visible' => 'Category_Is_Visible',
        'category_meta_title' => 'Category_Meta_Title',
        'category_meta_description' => 'Category_Meta_Description',
        'category_meta_noindex' =>'Category_Meta_Noindex',
        'category_delete' =>'Category_Delete',
        'category_filters' =>  'Category_Filters',
        'category_default_sorting' => 'Category_Default_Sorting',
        'category_parent' => 'Category_Parent',
        'category_reference' => 'Category_Reference',
        'category_img' => 'Category_Img',
        'category_img_thumbnail' => 'Category_Img_Thumbnail',
        'category_img_alt' => 'Category_Img_Alt',
        'category_description' => 'Category_Description',
        'category_language' => 'Category_Language',
        'category_country' => 'Category_Country'

    ];

	public function index(Request $request, $page = 1)
	{

		if ($request->isXmlHttpRequest()){
      
			$pagination_fields = $this->getDataTablesInfoForQuery($request->input());

			$categories = Category::paginateForAdmin($pagination_fields);

			$total_categories = Category::getCategoriesCount();

			return response()->json($this->convertPaginatorToDataTableInput($categories, $total_categories));
		}

		return view('category/index');
	}

	public function import()
	{
		return view('category/importer');
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

		if (strtolower($file->getClientOriginalExtension()) != 'xls') {
			return new JsonResponse(array(
				'status' => 'fail',
				'message' => trans('json.needValidCSV')
			));
		}

		$disk = \Storage::disk('local');

		if (!$disk->exists('feeds/categories')){
			$disk->makeDirectory('feeds/categories');
		}

		//the filename will renamed to filename - upload date in the format Y-m-d H:i:s and the file extension

		$uploaded_date = new \DateTime('now', new \DateTimeZone('UTC'));
		$uploaded_date = $uploaded_date->format('Y-m-d H:i:s');

		$name_without_extension = str_replace('.'.$file->getClientOriginalExtension(), '' ,$file->getClientOriginalName());

		$new_filename = $name_without_extension.' - '.$uploaded_date.'.'.$file->getClientOriginalExtension();

		$file->move(storage_path('app/feeds/categories').DIRECTORY_SEPARATOR, $new_filename);

		return new JsonResponse(array(
			'status' => 'success',
			'filename' => $new_filename,
		));
	}

	private function validate_fields_array($fields)
	{
		$required_fields = array(
			'category_id',
			'categories',
			'category_short_description',
			'category_title',
			'category_url_key',
			'category_is_visible',
			'category_meta_title',
			'category_meta_description',
			'category_meta_noindex',
			'category_delete',
			'category_filters',
			'category_default_sorting',
			'category_parent',
            'category_reference',
            'category_img',
            'category_img_thumbnail',
            'category_img_alt',
            'category_description',
            'category_language',
            'category_country'
		);
		return array_diff($required_fields, $fields);
	}

	public function importStarter(Request $request){

        Excel::create('Plantilla', function($excel)  {

            $excel->sheet('Categories', function ($sheet) {

                $sheet->appendRow($this->required_fields);
            });

        })->export('xls');


	}

	public function export(){

		$filename = 'Categorías - '.date('d-m-Y H:i:s', time());

		$export = new Export();
		$export->user_id = \Auth::user()->id;
		$export->filename = $filename . '.xls';
		$export->type = 'categories';

		$export->save();

		$disk = \Storage::disk('local');

		if (!$disk->exists('exports/categories')){
			$disk->makeDirectory('exports/categories');
		}

		set_time_limit(0);

		$categories = Category::forExport();

        Excel::create($filename, function($excel) use ($categories){
            $excel->sheet('Categories', function($sheet) use ($categories) {

                $sheet->appendRow($this->required_fields);

                foreach($categories as $category){

                    $resultArray = json_decode(json_encode($category), true);

                    $resultArray = array_map(function($value){ return utf8_decode($value); }, $resultArray);

                    //replace the boolean to the correponding value
                    $resultArray['Category_Is_Visible'] = $resultArray['Category_Is_Visible'] == 1 ? 'yes' : 'no';
                    $resultArray['Category_Meta_Noindex'] = $resultArray['Category_Meta_Noindex'] == 1 ? 'yes' : 'no';
                    $resultArray['Category_Img'] = ($resultArray['Category_Img'] != null || !empty($resultArray['Category_Img'])) ? asset('images/categories/img') . '/'.$resultArray['Category_Img'] : '';
                    $resultArray['Category_Img_Thumbnail'] = ($resultArray['Category_Img_Thumbnail'] != null || !empty($resultArray['Category_Img_Thumbnail'])) ? asset('images/categories/img_thumbnail') . '/'.$resultArray['Category_Img_Thumbnail'] : '';

                    $resultArray = array_map(function($value){

                        return preg_replace( "/\r|\n/", "", $value );

                    }, $resultArray);

                    $sheet->appendRow($resultArray);

                }

            });

            })->export('xls');




	}

	public function doImport(Request $request) {
    set_time_limit(0);
		$file_path = storage_path('app/feeds/categories').DIRECTORY_SEPARATOR.urldecode($request->input('file'));

		if (!file_exists($file_path)) {
      return new JsonResponse(
        [
				  'status' => 'fail',
				  'message' => trans('json.errorOpenFile')
			 ]
      );
		}


		$log_file_name = 'import_' . date('Y-m-d-H:i:s') . '.log';
		$log_file_handle = fopen(storage_path('logs/') . $log_file_name, 'w');
		$totals = [
			'removed' => [
				'count' => 0,
				'done' => 0
			],
			'added' => [
				'count' => 0,
				'done' => 0,
			],
			'updated' => [
				'count' => 0,
				'done' => 0
			]
		];

		$fields = null;
		$file_errors = false;
		$error_on_row = false;
		$deleted_rows = array();
        $country = '';
        $language = '';

        Excel::filter('chunk')->load($file_path)->chunk(250,function($results) use (&$fields, &$deleted_rows, $log_file_handle, &$totals, &$file_errors, $error_on_row,&$country,&$language){

            $firstRow = $results->first()->keys()->toArray();

            $results =  $results->toArray();

            $missing_fields = $this->validate_fields_array(array_values($firstRow));

            if (count($missing_fields) > 0) {
                $missing_fields_error = '';

                foreach ($missing_fields as $field) {
                    $missing_fields_error .= 'En el CSV no podemos encontrar el campo obligatorio ' . $field . '<br/>';
                }

                return new JsonResponse(
                    [
                        'status' => 'fail',
                        'message' => $missing_fields_error
                    ]
                );
            }

            foreach($results as $row) {
                $tempRow = $row;
                $row = [];
                foreach ($tempRow as $key => $value)
                {
                    if (isset($this->mapedKeys[$key]))
                    {
                        $row[$this->mapedKeys[$key]] = $value;
                    }
                    else
                    {
                        $row[$key] = $value;
                    }
                }

                    try {
                        $value = $row;

                    } catch (\Exception $e) {
                        $error_on_row = true;
                    }

                    if ($error_on_row) {
                        $error_on_row = false;
                        fwrite($log_file_handle, 'Error importando: ' . $row . "\r\n");
                        $file_errors = true;
                    } else {
                        \DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
                        if ((isset($value['Category_Delete']) && strtolower($value['Category_Delete']) == 'yes') || in_array($value['Category_Parent'], $deleted_rows)) {

                            try {
                                \DB::beginTransaction();
                                $totals['removed']['count']++;
                                \DB::table('categories')->where('id', '=', $value['Category_ID'])->delete();
                                \DB::commit();
                                $totals['removed']['done']++;
                                $deleted_rows[] = $value['Category_ID'];
                            } catch (\Exception $e) {
                                \DB::rollBack();
                                $file_errors = true;
                                fwrite($log_file_handle, 'Error eliminando ' . $value['Category_Title'] . ' Detalles: ' . $e->getMessage());
                            }
                        }
                        else {
                            $found = \DB::table('categories')->select()->where('id', '=', $value['Category_ID'])->get();

                            $language = (isset($value['Category_Language']) ? $value['Category_Language'] : get_current_language());
                            $country = (isset($value['Category_Country']) ? $value['Category_Country'] : get_current_country());

                            if ($found != null) {
                                  try {
                                    $totals['updated']['count']++;
                                    \DB::beginTransaction();
                                    \DB::table('categories')->where('id', '=', $value['Category_ID'])->update(
                                        array(
                                            'id' => $value['Category_ID'],
                                            'categories' => utf8_encode($value['Categories']),
                                            'short_description' => utf8_encode($value['Category_Short_Description']),
                                            'title' => utf8_encode($value['Category_Title']),
                                            'url_key' => addslashes($value['Category_url_key']),
                                            'is_visible' => strtolower($value['Category_Is_Visible']) == 'yes' ? 1 : 0,
                                            'meta_title' => utf8_encode($value['Category_Meta_Title']),
                                            'meta_description' => utf8_encode($value['Category_Meta_Description']),
                                            'meta_no_index' => strtolower($value['Category_Meta_Noindex']) == 'yes' ? 1 : 0,
                                            'filters' => utf8_encode($value['Category_Filters']),
                                            'default_sorting' => utf8_encode($value['Category_Default_Sorting']),
                                            'parent_id' => $value['Category_Parent'] !== '' ? $value['Category_Parent'] : null,
                                            'reference' => $value['Category_Reference'] !== '' ? $value['Category_Reference'] : null,
                                            'img' =>  isset($value['Category_Img']) ? substr($value['Category_Img'], strrpos($value['Category_Img'], '/') + 1) : null ,
                                            'img_thumbnail' => isset($value['Category_Img_Thumbnail']) ? substr($value['Category_Img_Thumbnail'], strrpos($value['Category_Img_Thumbnail'], '/') + 1) : null,
                                            'img_alt' => isset($value['Category_Img_Alt']) ? $value['Category_Img_Alt'] : null,
                                            'description' => $value['Category_Description'] !== '' ? $value['Category_Description'] : null,
                                            'language' => $language,
                                            'country' => $country
                                        )
                                    );
                                    \DB::commit();
                                    $totals['updated']['done']++;
                                } catch (\Exception $e) {
                                    \DB::rollBack();
                                    $file_errors = true;
                                    fwrite($log_file_handle, 'Error Actualizando ' . $value['Category_Title'] . ' Detalles: ' . $e->getMessage());
                                }

                            } else {

                                try {
                                    $totals['added']['count']++;
                                    \DB::beginTransaction();
                                    \DB::table('categories')->insertGetId(
                                        array(
                                            'id' => $value['Category_ID'],
                                            'categories' => utf8_encode($value['Categories']),
                                            'short_description' => utf8_encode($value['Category_Short_Description']),
                                            'title' => utf8_encode($value['Category_Title']),
                                            'url_key' => addslashes($value['Category_url_key']),
                                            'is_visible' => strtolower($value['Category_Is_Visible']) == 'yes' ? 1 : 0,
                                            'meta_title' => utf8_encode($value['Category_Meta_Title']),
                                            'meta_description' => utf8_encode($value['Category_Meta_Description']),
                                            'meta_no_index' => strtolower($value['Category_Meta_Noindex']) == 'yes' ? 1 : 0,
                                            'filters' => utf8_encode($value['Category_Filters']),
                                            'default_sorting' => utf8_encode($value['Category_Default_Sorting']),
                                            'parent_id' => $value['Category_Parent'] !== '' ? $value['Category_Parent'] : null,
                                            'reference' => $value['Category_Reference'] !== '' ? $value['Category_Reference'] : null,
                                            'img' =>  isset($value['Category_Img']) ? substr($value['Category_Img'], strrpos($value['Category_Img'], '/') + 1) : null ,
                                            'img_thumbnail' => isset($value['Category_Img_Thumbnail']) ? substr($value['Category_Img_Thumbnail'], strrpos($value['Category_Img_Thumbnail'], '/') + 1) : null,
                                            'img_alt' => isset($value['Category_Img_Alt']) ? $value['Category_Img_Alt'] : null,
                                            'description' => $value['Category_Description'] !== '' ? $value['Category_Description'] : null,
                                            'language' => $language,
                                            'country' => $country
                                        )
                                    );
                                    \DB::commit();
                                    $totals['added']['done']++;
                                } catch (\Exception $e) {
                                    \DB::rollBack();
                                    fwrite($log_file_handle, 'Error creando ' . $value['Category_Title'] . ' Detalles: ' . $e->getMessage());
                                    $file_errors = true;
                                }

                            }

                        }

                    }

            }
        },false);

		$import = new Import();
		$import->user_id = \Auth::getUser()->id;
		$import->type = 'categories';
		$import->filename = $request->input('file');
		$import->log_file = $log_file_name;
		$import->totals = serialize($totals);
		$import->save();

		if ($file_errors) {
      return new JsonResponse(
        [
				  'status' => 'success',
				  'message' => trans('json.importedCategoriesError'),
				  'results' => \View::make('product/import_results', ['import' => $import, 'totals' => $totals])->render()
        ]
      );
		} 
    
    else {
      return new JsonResponse(
        [
				  'status' => 'success',
				  'message' => trans('json.importedCategoriesSuccess'),
				  'results' => \View::make('product/import_results', ['import' => $import, 'totals' => $totals])->render(),
                  'country' => $country,
                  'language' => $language
        ]
      );
    }
  
  }

	public function updateCategoryTreeAction(Request $request){

		try{
    
			$service = app('MainService');

			$service->updateCategoryTree($request->input('language'),$request->input('country'));
			$service->updateCategoriesTreeCache($request->input('language'),$request->input('country'));

			return new JsonResponse([
				'status' => 'success',
				'message' => trans('json.updatedCategoriesTree')
			]);

		} catch (\Exception $e){

			return new JsonResponse([
				'status' => 'fail',
				'message' => trans('json.updatedCategoriesTreeError')
			]);

		}

	}

	public function updateCategoryTreeCacheAction(Request $request){

		try{

			$service = app('MainService');

			$service->updateCategoriesTreeCache();

			return new JsonResponse([
				'status' => 'success',
				'message' => trans('json.updatedCategoriesTree')
			]);

		} catch (\Exception $e){

			return new JsonResponse([
				'status' => 'fail',
				'message' => trans('json.updatedCategoriesTreeError')
			]);

		}

	}

	public function iframeTest(){

		$iframeHeader = base_path('resources/views/product/iframeHeader.html');
		$iframeFooter = base_path('resources/views/product/iframeBottom.html');

		$header = file_get_contents($iframeHeader);

		$bottom = file_get_contents($iframeFooter);

		echo $header;

		set_time_limit(0);
		ob_implicit_flush(true);
		ob_end_flush();

		$result_types = array('warning', 'danger', 'success');

		for($i = 0; $i < 100; $i++){
			//Hard work!!
			sleep(1);
			$p = ($i+1)*10; //Progress

			$result_type = $result_types[rand(0, count($result_types) - 1)];

			echo '<tr class="'.$result_type.'">
					<td class="text-center"><script>updateAutoscroll()</script>'.date('Y-m-d H:i:s', time()).'</td>
                    <td class="text-center"><span class="label label-primary">Ok</span></td>
                    <td class="text-left">Importando producto '.$i.'</td>
                </tr>';
		}

		echo $bottom;
		die();
	}

	public function editForm($id){

		$category = Category::find($id);

		if (!$category){
			\Session::flash('error', trans('flash.error.category_not_found') );
			return redirect(route('admin_categories_list'));
		}

		$other_categories = Category::where('id', '<>', $id)->where('country',get_current_country())->where('language',get_current_language() )->get();

		return view('category.form', array('category' => $category, 'other_categories' => $other_categories));
	}

	public function editFormProcess(Requests\CategoryFormRequest $request, $id){

		try{

			$category = Category::find($id);

			$needs_re_routing = $category->url_key != $request->input('category_url');

            $category->update($request->only([
                'reference','title','categories','parent_id','url_key',
                'short_description','is_visible','default_sorting','filters','meta_title',
                'meta_description','meta_no_index','img_alt','description'
            ]));

            foreach ($request->file() as $attr => $fileObj)
            {
                $filePath = Uploader::upload($fileObj,public_path('images/categories').'/'.$attr);
                $category->{$attr} = $filePath;
                $category->save();
            }

			$service = app('MainService');

			if ($needs_re_routing){
				$app = \App::getFacadeRoot();

				$service->generateCategoryRoutes();
			}

			try{

				$service->updateCategoryTree();
				$service->updateCategoriesTreeCache();
				$service->updateMenuConfigurationCache();

			} catch (\Exception $e){

				\Log::error($e->getMessage(), array('stack_trace' => $e->getTraceAsString()));

				\Session::flash('error', trans('flash.error.cannot_update_tree') );
				return redirect(route('admin_categories_list'));
			}

			\Session::flash('success', trans('flash.success.operation_done_successfully') );

			return redirect(route('admin_categories_list'));

		} catch (\Exception $e){
			throw $e;
		}
	}

	public function batchAction(Request $request){

		if (!$request->isXmlHttpRequest()){
			abort();
		}

		if ($request->get('action') == 'remove'){

			$ids = $request->input('ids', null);

			if ($ids){
				try{
					foreach($ids as $id){
						\DB::table('categories')->delete($id);
					}

					app('MainService')->updateCategoriesTreeCache();

					return response()->json(['status' => 'success', 'message' => trans('json.operationDoneSuccessfully') ]);

				} catch (\Exception $e){

					\Log::error($e->getMessage());

					return response()->json(['status' => 'success', 'message' => trans('json.errorRemoveElement')]);
				}
			}

			return response()->json(['status' => 'success', 'message' => trans('json.mustSelectElements') ]);
		}

		if ($request->get('action') == 'toggle'){

			$ids = $request->input('ids', null);

			if ($ids){
				try{
					foreach($ids as $id){
						$cat = Category::find($id);

						if ($cat){
							$cat->is_visible = $cat->is_visible == true ? false : true;

							$cat->save();

						}
					}

					app('MainService')->updateCategoriesTreeCache();

					return response()->json(['status' => 'success', 'message' => trans('json.operationDoneSuccessfully') ]);

				} catch (\Exception $e){

					\Log::error($e->getMessage());

					return response()->json(['status' => 'success', 'message' => trans('json.errorRemoveElement')]);
				}
			}

			return response()->json(['status' => 'success', 'message' => trans('json.mustSelectElements') ]);
		}
	}

	public function sortingAction(Request $request){

		$categories_tree = null;

    $country = get_current_country();
    $language = get_current_language(); 

		if (\Cache::has('categories_tree'.'_'.$country.'_'.$language)){
			$categories_tree = \Cache::get('categories_tree'.'_'.$country.'_'.$language);
		} else {
			$categories_tree = Category::updateFullCategoryHierarchy();
			\Cache::forever('categories_tree'.'_'.$country.'_'.$language, $categories_tree);
		}

		return view('category/sorting', ['categories_tree' => $categories_tree]);
	}

	public function updateSortingAction(Request $request){

		if ($request->has('nodes') && $request->input('nodes')){

			$nodes = json_decode($request->input('nodes'), true);

			try{

				\DB::beginTransaction();

				foreach($nodes as $node){

					\DB::table('categories')->where('id', '=', $node['id'])
						->update(array(
							'parent_id' => isset($node['parent_id']) && $node['parent_id'] ? $node['parent_id'] : null,
							'position' => $node['pos'],
							'lft' => $node['left'],
							'rgt' => $node['right']
						));

				}

				\DB::commit();

				$service = app('MainService');

				$service->updateCategoryTree();

				$tree = $service->updateCategoriesTreeCache();

				return response()->json([
					'status' => 'success',
				]);

			} catch(\Exception $e){

				throw $e;

				\DB::rollBack();

				\Log::error('Error sorting categories: ' . $e->getMessage());

				return response()->json([
					'status' => 'error',
				]);

			}


		}

	}


}

