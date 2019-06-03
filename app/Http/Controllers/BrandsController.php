<?php namespace ProjectCarrasco\Http\Controllers;

use ProjectCarrasco\Http\Requests;
use ProjectCarrasco\Http\Controllers\Controller;
use ProjectCarrasco\Brand;
use ProjectCarrasco\Setup;
use ProjectCarrasco\Http\Requests\BrandUpdateRequest;
use Illuminate\Http\Request;
use ProjectCarrasco\Helpers\Uploader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Excel;
use ProjectCarrasco\Import;

class BrandsController extends Controller {

    private $mapedKeys = [
        'brand_id' =>  'Brand_ID',
        'brand_title' => 'Brand_Title',
        'brand_short_description' => 'Brand_Short_Description',
        'brand_name' => 'Brand_Name',
        'brand_url_key' => 'Brand_url_key',
        'brand_is_visible' =>'Brand_Is_Visible',
        'brand_meta_title' => 'Brand_Meta_Title',
        'brand_meta_description' => 'Brand_Meta_Description',
        'brand_meta_noindex' => 'Brand_Meta_Noindex',
        'brand_delete' => 'Brand_Delete',
        'brand_default_sorting' => 'Brand_Default_Sorting',
        'brand_image' => 'Brand_Image',
        'brand_description' => 'Brand_Description',
        'brand_language' => 'Brand_Language',
        'brand_country' => 'Brand_Country'
    ];
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
    {
        return view('brands.index');
    }

    public function indexDataTable(Request $request, $page = 1)
    {
        $pagination_fields = $this->getDataTablesInfoForQuery($request->input());

        $brands = Brand::paginateForAdmin($pagination_fields);

        $total_brands = Brand::getBrandsCount();

        return response()->json($this->convertPaginatorToDataTableInput($brands, $total_brands));

    }


    public function modifyVisibility(Request $request)
    {
        try{
            foreach($request->input('ids') as $id){
                $brand = Brand::find($id);

                if ($brand){
                    $brand->is_visible = ($brand->is_visible == true  || $brand->is_visible == 1 ) ? 0 : 1;

                    $brand->save();

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
               Brand::whereIn('id',$ids)->delete();

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
        $brand = Brand::find($id);

        if (!$brand){
            \Session::flash('error', trans('flash.error.brand_not_found') );
            return redirect(route('admin.brands.index'));
        }
        $setups = Setup::all();

        return view('brands.edit', ['brand' => $brand , 'setups' => $setups]);

    }


	public function update(BrandUpdateRequest $request,$id)
	{
        try{

            $brand = Brand::find($id);

            $brand->update($request->only([
                'name','title','url_key',
                'short_description','is_visible','default_sorting','meta_title',
                'meta_description','meta_noindex','description'
            ]));

            foreach ($request->file() as $attr => $fileObj)
            {
                $filePath = Uploader::upload($fileObj,public_path('images/brands').'/'.$attr);
                $brand->{$attr} = $filePath;

            }
            $setup = Setup::find($request->input('brand_country'));

            $brand->country = $setup->country_abre;
            $brand->language = $setup->language_abre;
            $brand->save();
            app('MainService')->updateBrandsVirtualRoutes($setup->country_abre,$setup->language_abre);
            \Session::flash('success', trans('flash.success.operation_done_successfully') );

            return redirect(route('admin.brands.index'));

        } catch (\Exception $e){
            throw $e;
        }
	}

    public function export()
    {
        $filename = 'Brands - '.date('d-m-Y H:i:s', time());

        set_time_limit(0);

        $brands = Brand::forExport();

        Excel::create($filename, function($excel) use ($brands){
            $excel->sheet('Brands', function($sheet) use ($brands) {

                $sheet->appendRow(Brand::$requiredFields);

                foreach($brands as $brand){

                    $resultArray = json_decode(json_encode($brand), true);

                    $resultArray = array_map(function($value){ return utf8_decode($value); }, $resultArray);

                    //replace the boolean to the correponding value
                    $resultArray['Brand_Is_Visible'] = $resultArray['Brand_Is_Visible'] == 1 ? 'yes' : 'no';
                    $resultArray['Brand_Meta_Noindex'] = $resultArray['Brand_Meta_Noindex'] == 1 ? 'yes' : 'no';
                    $resultArray['Brand_Image'] = ($resultArray['Brand_Image'] != null || !empty($resultArray['Brand_Image'])) ? asset('images/brands/image') . '/'.$resultArray['Brand_Image'] : '';

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

            $excel->sheet('Brands', function ($sheet) {

                $sheet->appendRow(Brand::$requiredFields);
            });

        })->export('xls');


    }

    public function importView()
    {
        return view('brands.import');
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

        if (!$disk->exists('feeds/brands')){
            $disk->makeDirectory('feeds/brands');
        }

        //the filename will renamed to filename - upload date in the format Y-m-d H:i:s and the file extension

        $uploaded_date = new \DateTime('now', new \DateTimeZone('UTC'));
        $uploaded_date = $uploaded_date->format('Y-m-d H:i:s');

        $name_without_extension = str_replace('.'.$file->getClientOriginalExtension(), '' ,$file->getClientOriginalName());

        $new_filename = $name_without_extension.' - '.$uploaded_date.'.'.$file->getClientOriginalExtension();

        $file->move(storage_path('app/feeds/brands').DIRECTORY_SEPARATOR, $new_filename);

        return new JsonResponse(array(
            'status' => 'success',
            'filename' => $new_filename,
        ));
    }

    public function doImport(Request $request)
    {
        set_time_limit(0);
        $file_path = storage_path('app/feeds/brands').DIRECTORY_SEPARATOR.urldecode($request->input('file'));

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
                        if ((isset($value['Brand_Delete']) && strtolower($value['Brand_Delete']) == 'yes')) {

                            try {
                                \DB::beginTransaction();
                                $totals['removed']['count']++;
                                \DB::table('brands')->where('id', '=', $value['Brand_ID'])->delete();
                                \DB::commit();
                                $totals['removed']['done']++;
                                $deleted_rows[] = $value['Brand_ID'];
                            } catch (\Exception $e) {
                                \DB::rollBack();
                                $file_errors = true;
                                fwrite($log_file_handle, 'Error eliminando ' . $value['Brand_Title'] . ' Detalles: ' . $e->getMessage());
                            }
                        }
                        else {
                            $found = Brand::find($value['Brand_ID']);

                            $language = (isset($value['Brand_Language']) ? $value['Brand_Language'] : get_current_language());
                            $country = (isset($value['Brand_Country']) ? $value['Brand_Country'] : get_current_country());

                            if ($found != null) {

                                try {

                                    $totals['updated']['count']++;
                                    \DB::beginTransaction();
                                    \DB::table('brands')->where('id', '=', $value['Brand_ID'])->update(
                                        array(
                                            'short_description' => utf8_encode($value['Brand_Short_Description']),
                                            'title' => utf8_encode($value['Brand_Title']),
                                            'url_key' => addslashes($value['Brand_url_key']),
                                            'is_visible' => strtolower($value['Brand_Is_Visible']) == 'yes' ? 1 : 0,
                                            'meta_title' => utf8_encode($value['Brand_Meta_Title']),
                                            'meta_description' => utf8_encode($value['Brand_Meta_Description']),
                                            'meta_noindex' => strtolower($value['Brand_Meta_Noindex']) == 'yes' ? 1 : 0,
                                            'default_sorting' => utf8_encode($value['Brand_Default_Sorting']),
                                            'image' =>  isset($value['Brand_Image']) ? substr($value['Brand_Image'], strrpos($value['Brand_Image'], '/') + 1) : null ,
                                            'description' => $value['Brand_Description'] !== '' ? $value['Brand_Description'] : null,
                                            'language' => $language,
                                            'country' => $country
                                        )
                                    );
                                    \DB::commit();
                                    $totals['updated']['done']++;
                                } catch (\Exception $e) {
                                    \DB::rollBack();
                                    $file_errors = true;
                                    fwrite($log_file_handle, 'Error Actualizando ' . $value['Brand_Title'] . ' Detalles: ' . $e->getMessage());
                                }

                            } else {

                                try {
                                    $totals['added']['count']++;
                                    \DB::beginTransaction();
                                    \DB::table('brands')->insertGetId(
                                        array(
                                            'short_description' => utf8_encode($value['Brand_Short_Description']),
                                            'title' => utf8_encode($value['Brand_Title']),
                                            'url_key' => addslashes($value['Brand_url_key']),
                                            'is_visible' => strtolower($value['Brand_Is_Visible']) == 'yes' ? 1 : 0,
                                            'meta_title' => utf8_encode($value['Brand_Meta_Title']),
                                            'meta_description' => utf8_encode($value['Brand_Meta_Description']),
                                            'meta_noindex' => strtolower($value['Brand_Meta_Noindex']) == 'yes' ? 1 : 0,
                                            'default_sorting' => utf8_encode($value['Brand_Default_Sorting']),
                                            'image' =>  isset($value['Brand_Image']) ? substr($value['Brand_Image'], strrpos($value['Brand_Image'], '/') + 1) : null ,
                                            'description' => $value['Brand_Description'] !== '' ? $value['Brand_Description'] : null,
                                            'language' => $language,
                                            'country' => $country
                                        )
                                    );
                                    \DB::commit();
                                    $totals['added']['done']++;
                                } catch (\Exception $e) {
                                    \DB::rollBack();
                                    fwrite($log_file_handle, 'Error creando ' . $value['Brand_Title'] . ' Detalles: ' . $e->getMessage());
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
        $import->type = 'brands';
        $import->filename = $request->input('file');
        $import->log_file = $log_file_name;
        $import->totals = serialize($totals);
        $import->save();



        if ($file_errors) {
            return new JsonResponse(
                [
                    'status' => 'success',
                    'message' => trans('json.importedBrandsError'),
                    'results' => \View::make('product/import_results', ['import' => $import, 'totals' => $totals])->render()
                ]
            );
        }

        else {
            try {
                set_time_limit(0);
                \Log::info('Starting updateRoutesAction');



                app('MainService')->updateBrandsVirtualRoutes();


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
                    'message' => trans('json.importedBrandsSuccess'),
                    'results' => \View::make('product/import_results', ['import' => $import, 'totals' => $totals])->render()
                ]
            );
        }
    }

    private function validate_fields_array($fields)
    {

        return array_diff(Brand::$requiredFields, $fields);
    }

}
