<?php namespace ProjectCarrasco\Http\Controllers;

use ProjectCarrasco\Http\Requests;
use ProjectCarrasco\Http\Controllers\Controller;
use ProjectCarrasco\Store;
use ProjectCarrasco\Setup;
use ProjectCarrasco\Http\Requests\StoreUpdateRequest;
use Illuminate\Http\Request;
use ProjectCarrasco\Helpers\Uploader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Excel;
use ProjectCarrasco\Import;

class StoresController extends Controller {

    private $mapedKeys = [
        'store_id' =>  'Store_ID',
        'store_name' => 'Store_Name',
        'store_url_key' => 'Store_url_key',
        'store_is_visible' =>'Store_Is_Visible',
        'store_meta_title' => 'Store_Meta_Title',
        'store_meta_description' => 'Store_Meta_Description',
        'store_meta_noindex' => 'Store_Meta_Noindex',
        'store_delete' => 'Store_Delete',
        'store_logo' => 'Store_Logo',
        'store_logo_thumb' => 'Store_Logo_Thumb',
        'store_language' => 'Store_Language',
        'store_country' => 'Store_Country'
    ];
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
    {
        return view('stores.index');
    }

    public function indexDataTable(Request $request, $page = 1)
    {
        $pagination_fields = $this->getDataTablesInfoForQuery($request->input());

        $stores = Store::paginateForAdmin($pagination_fields);

        $total_brands = Store::getStoresCount();

        return response()->json($this->convertPaginatorToDataTableInput($stores, $total_brands));

    }


    public function modifyVisibility(Request $request)
    {
        try{
            foreach($request->input('ids') as $id){
                $store = Store::find($id);

                if ($store){
                    $store->is_visible = ($store->is_visible == true  || $store->is_visible == 1 ) ? 0 : 1;

                    $store->save();

                }
            }

            return response()->json(['status' => 'success', 'message' => trans('json.operationDoneSuccessfully') ]);

        } catch (\Exception $e){
            \Log::error($e->getMessage());

            return response()->json(['status' => 'success', 'message' => trans('json.errorRemoveElement')]);
        }
	}

    public function destroy(Request $request)
    {
        $ids = $request->input('ids', null);

        if ($ids)
        {
            try{
               Store::whereIn('id',$ids)->delete();

                return response()->json(['status' => 'success', 'message' => trans('json.operationDoneSuccessfully') ]);

            } catch (\Exception $e){

                \Log::error($e->getMessage());

                return response()->json(['status' => 'success', 'message' => trans('json.errorRemoveElement')]);
            }
        }

        return response()->json(['status' => 'success', 'message' => trans('json.mustSelectElements') ]);
    }
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        $store = Store::find($id);

        if (!$store){
            \Session::flash('error', trans('flash.error.brand_not_found') );
            return redirect(route('admin.stores.index'));
        }
        $setups = Setup::all();

        return view('stores.edit', ['store' => $store , 'setups' => $setups]);
    }


	public function update(StoreUpdateRequest $request,$id)
	{
        try{
            $store = Store::find($id);

            $store->update($request->only([
                'name','url_key',
                'is_visible','meta_title',
                'meta_description','meta_noindex','description'
            ]));

            foreach ($request->file() as $attr => $fileObj)
            {
                $fileName = Uploader::upload($fileObj,public_path('images/stores').'/'.$attr);
                $store->{$attr} = $fileName;
            }

            $setup = Setup::find($request->input('stores_country'));

            if (!is_null($setup))
            {
                $store->country = $setup->country_abre;
                $store->language = $setup->language_abre;
            }

            $store->save();

            app('MainService')->updateStoresVirtualRoutes($setup->country_abre,$setup->language_abre);

            \Session::flash('success', trans('flash.success.operation_done_successfully') );

            return redirect(route('admin.stores.index'));

        } catch (\Exception $e){
            throw $e;
        }
	}

    public function export()
    {
        $filename = 'Stores - '.date('d-m-Y H:i:s', time());

        set_time_limit(0);

        $stores = Store::forExport();

        Excel::create($filename, function($excel) use ($stores){
            $excel->sheet('Stores', function($sheet) use ($stores) {

                $sheet->appendRow(Store::$requiredFields);

                foreach($stores as $store){

                    $resultArray = json_decode(json_encode($store), true);

                    $resultArray = array_map(function($value){ return utf8_decode($value); }, $resultArray);

                    //replace the boolean to the correponding value
                    $resultArray['Store_Is_Visible'] = $resultArray['Store_Is_Visible'] == 1 ? 'yes' : 'no';
                    $resultArray['Store_Meta_Noindex'] = $resultArray['Store_Meta_Noindex'] == 1 ? 'yes' : 'no';
                    $resultArray['Store_Logo'] = ($resultArray['Store_Logo'] != null || !empty($resultArray['Store_Logo'])) ? asset('images/stores/logo') . '/'.$resultArray['Store_Logo'] : '';
                    $resultArray['Store_Logo_Thumb'] = ($resultArray['Store_Logo_Thumb'] != null || !empty($resultArray['Store_Logo_Thumb'])) ? asset('images/stores/logo_thumb') . '/'.$resultArray['Store_Logo_Thumb'] : '';


                    $resultArray = array_map(function($value){

                        return preg_replace( "/\r|\n/", "", $value );

                    }, $resultArray);

                    $sheet->appendRow($resultArray);

                }

            });

        })->export('xls');

    }

    public function exportBlankTemplate()
    {
        Excel::create('Plantilla', function($excel)  {

            $excel->sheet('Sotres', function ($sheet) {

                $sheet->appendRow(Store::$requiredFields);
            });

        })->export('xls');


    }

    public function importView()
    {
        return view('stores.import');
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

        if (!$disk->exists('feeds/stores')){
            $disk->makeDirectory('feeds/stores');
        }

        //the filename will renamed to filename - upload date in the format Y-m-d H:i:s and the file extension

        $uploaded_date = new \DateTime('now', new \DateTimeZone('UTC'));
        $uploaded_date = $uploaded_date->format('Y-m-d H:i:s');

        $name_without_extension = str_replace('.'.$file->getClientOriginalExtension(), '' ,$file->getClientOriginalName());

        $new_filename = $name_without_extension.' - '.$uploaded_date.'.'.$file->getClientOriginalExtension();

        $file->move(storage_path('app/feeds/stores').DIRECTORY_SEPARATOR, $new_filename);

        return new JsonResponse(array(
            'status' => 'success',
            'filename' => $new_filename,
        ));
    }

    public function doImport(Request $request)
    {
        set_time_limit(0);
        $file_path = storage_path('app/feeds/stores').DIRECTORY_SEPARATOR.urldecode($request->input('file'));

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

        Excel::filter('chunk')->load($file_path)->chunk(250,function($results) use (&$fields, &$deleted_rows, $log_file_handle, &$totals, &$file_errors, $error_on_row){

            $first_line = true;
            $appendRows = false;
            $results = $results->toArray();


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

                if ($first_line == true) {
                    $first_line = false;

                    $missing_fields = $this->validate_fields_array(array_keys($row));

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
                    $appendRows = true;
                }

                if ($appendRows) {

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
                        if ((isset($value['Store_Delete']) && strtolower($value['Store_Delete']) == 'yes')) {

                            try {
                                \DB::beginTransaction();
                                $totals['removed']['count']++;
                                \DB::table('stores')->where('id', '=', $value['Store_ID'])->delete();
                                \DB::commit();
                                $totals['removed']['done']++;
                                $deleted_rows[] = $value['Store_ID'];
                            } catch (\Exception $e) {
                                \DB::rollBack();
                                $file_errors = true;
                                fwrite($log_file_handle, 'Error eliminando ' . $value['Store_Name'] . ' Detalles: ' . $e->getMessage());
                            }
                        }
                        else {
                            $found = Store::find($value['Store_ID']);

                            $language = (isset($value['Store_Language']) ? $value['Store_Language'] : get_current_language());
                            $country = (isset($value['Store_Country']) ? $value['Store_Country'] : get_current_country());

                            if ($found != null) {

                                try {

                                    $totals['updated']['count']++;
                                    \DB::beginTransaction();
                                    \DB::table('stores')->where('id', '=', $value['Store_ID'])->update(
                                        array(
                                            'url_key' => addslashes($value['Store_url_key']),
                                            'is_visible' => strtolower($value['Store_Is_Visible']) == 'yes' ? 1 : 0,
                                            'meta_title' => utf8_encode($value['Store_Meta_Title']),
                                            'meta_description' => utf8_encode($value['Store_Meta_Description']),
                                            'meta_noindex' => strtolower($value['Store_Meta_Noindex']) == 'yes' ? 1 : 0,
                                            'logo' =>  isset($value['Store_Logo']) ? substr($value['Store_Logo'], strrpos($value['Store_Logo'], '/') + 1) : null ,
                                            'logo_thumb' =>  isset($value['Store_Logo_Thumb']) ? substr($value['Store_Logo_Thumb'], strrpos($value['Store_Logo_Thumb'], '/') + 1) : null ,
                                            'name' => utf8_encode($value['Store_Name']),
                                            'language' => $language,
                                            'country' => $country
                                         )
                                    );
                                    \DB::commit();
                                    $totals['updated']['done']++;
                                } catch (\Exception $e) {
                                    \DB::rollBack();
                                    $file_errors = true;
                                    fwrite($log_file_handle, 'Error Actualizando ' . $value['Store_Name'] . ' Detalles: ' . $e->getMessage());
                                }

                            } else {

                                try {
                                    $totals['added']['count']++;
                                    \DB::beginTransaction();
                                    \DB::table('stores')->insertGetId(
                                        array(
                                            'url_key' => addslashes($value['Store_url_key']),
                                            'is_visible' => strtolower($value['Store_Is_Visible']) == 'yes' ? 1 : 0,
                                            'meta_title' => utf8_encode($value['Store_Meta_Title']),
                                            'meta_description' => utf8_encode($value['Store_Meta_Description']),
                                            'meta_noindex' => strtolower($value['Store_Meta_Noindex']) == 'yes' ? 1 : 0,
                                            'logo' =>  isset($value['Store_Logo']) ? substr($value['Store_Logo'], strrpos($value['Store_Logo'], '/') + 1) : null ,
                                            'logo_thumb' =>  isset($value['Store_Logo_Thumb']) ? substr($value['Store_Logo_Thumb'], strrpos($value['Store_Logo_Thumb'], '/') + 1) : null ,
                                            'name' => utf8_encode($value['Store_Name']),
                                            'language' => $language,
                                            'country' => $country
                                        )
                                    );
                                    \DB::commit();
                                    $totals['added']['done']++;
                                } catch (\Exception $e) {
                                    \DB::rollBack();
                                    fwrite($log_file_handle, 'Error creando ' . $value['Store_Name'] . ' Detalles: ' . $e->getMessage());
                                    $file_errors = true;
                                }

                            }

                        }

                    }

                }

            }
        },false);

        $import = new Import();
        $import->user_id = \Auth::getUser()->id;
        $import->type = 'stores';
        $import->filename = $request->input('file');
        $import->log_file = $log_file_name;
        $import->totals = serialize($totals);
        $import->save();



        if ($file_errors) {
            return new JsonResponse(
                [
                    'status' => 'success',
                    'message' => trans('json.importedStoresError'),
                    'results' => \View::make('product/import_results', ['import' => $import, 'totals' => $totals])->render()
                ]
            );
        }

        else {
            try {
                set_time_limit(0);
                \Log::info('Starting updateRoutesAction');



                app('MainService')->updateStoresVirtualRoutes();


                \Log::info('Finishing updateRoutesAction');

            }

            catch (\Exception $e) {
                \Log::error($e->getMessage());
                return new JsonResponse(
                    [
                        'status' => 'fail',
                        'message' => trans('json.errorUpdatingRoutes')
                    ]
                );
            }
            return new JsonResponse(
                [
                    'status' => 'success',
                    'message' => trans('json.importedStoresSuccess'),
                    'results' => \View::make('product/import_results', ['import' => $import, 'totals' => $totals])->render()
                ]
            );
        }
    }

    private function validate_fields_array($fields)
    {
        return array_diff(Store::$requiredFields, $fields);
    }

}
