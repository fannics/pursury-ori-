<?php 

  namespace ProjectCarrasco\Http\Controllers;
  use ProjectCarrasco\Category;
  use ProjectCarrasco\Store;
  use ProjectCarrasco\Http\Requests;
  use Illuminate\Http\Request;
  use ProjectCarrasco\Import;
  use ProjectCarrasco\Product;
  use ProjectCarrasco\ProductCategory;                                          
  use ProjectCarrasco\ProductTag;
  use ProjectCarrasco\Setup;
  use ProjectCarrasco\Tags;
  use Symfony\Component\HttpFoundation\JsonResponse;
  use Excel;
  use ProjectCarrasco\Brand;

  class ProductController extends Controller {
        private  $required_fields = array(
            'Product_ID',
            'Product_Title',
            'Product_Category',
            'Product_Short_Description',
            'Product_Description',
            'Product_url_key',
            'Product_Is_Visible',
            'Product_Meta_Title',
            'Product_Image_Thumbnail',
            'Product_Image',
            'Product_Meta_Description',
            'Product_Meta_Noindex',
            'Product_Delete',
            'Product_Price',
            'Product_Ext_Link',
            'Product_Brand',
            'Product_Old_Price',
            'Product_Position',
            'Product_Country',
            'Product_Language',
            'Product_Image_Alt',
            'Product_Category_Sort',
            'Product_Parent',
            'Product_Store',
            'Product_Shipping_Cost',
            'Product_Stock',
            'Product_Parent_Filter',
            'Product_Is_Parent'
        );
      private $productPricesFields = [
          'Product_ID',
          'Product_Price',
          'Product_Old_Price',
      ];

      private $product_prices_validation_rules = [
          'Product_ID' => 'required',
          'Product_Old_Price' => 'numeric',
          'Product_Price' => 'required|numeric|min:0',
      ];
        private $mapedKeys = [
          'product_title' =>  'Product_Title',
          'product_id' =>  'Product_ID',
          'product_category' =>  'Product_Category',
          'product_short_description' =>  'Product_Short_Description',
          'product_description'=>  'Product_Description',
          'product_url_key' => 'Product_url_key',
           'product_is_visible' => 'Product_Is_Visible',
           'product_meta_title' => 'Product_Meta_Title',
            'product_image_thumbnail' =>'Product_Image_Thumbnail',
            'product_image' =>'Product_Image',
           'product_meta_description' =>  'Product_Meta_Description',
           'product_meta_noindex' => 'Product_Meta_Noindex',
            'product_delete' => 'Product_Delete',
           'product_price' => 'Product_Price',
           'product_ext_link' => 'Product_Ext_Link',
           'product_brand' => 'Product_Brand',
           'product_old_price' => 'Product_Old_Price',
           'product_country' => 'Product_Country',
           'product_language' => 'Product_Language',
           'product_image_alt' => 'Product_Image_Alt',
           'product_parent_filter' => 'Product_Parent_Filters',
            'product_stock' => 'Product_Stock',
            'product_shipping_cost' => 'Product_Shipping_Cost',
            'product_parent' => 'Product_Parent_ID',
            'product_is_parent' => 'Product_Is_Parent',
            'product_category_sort' => 'Product_Position',
            'product_store' => 'Product_Store',
            'product_winner' => 'Product_Winner'
        ];
    private $product_validation_rules = [
      'Product_Title' => 'required',
      'Product_ID' => 'required',
      'Product_Category' => 'required',
      'Product_Short_Description' => 'required',
      'Product_Description' => 'required',
      'Product_url_key' => 'required',
      'Product_Is_Visible' => 'required|in:yes,no,YES,NO,Yes,No,yEs,nO,yeS,yES,YEs',
      'Product_Meta_Title' => 'required',
      'Product_Image_Thumbnail' => 'required|url',
      'Product_Image' => 'required|url',
      'Product_Meta_Description' => 'required',
      'Product_Meta_Noindex' => 'required|in:yes,no,YES,NO,Yes,No,yEs,nO,yeS,yES,YEs',
      'Product_Delete' => 'in:yes,no,YES,NO,Yes,No,yEs,nO,yeS,yES,YEs',
      'Product_Price' => 'required|numeric|min:0',
      'Product_Ext_Link' => 'required|url',
      'Product_Old_Price' => 'nullable',
      'Product_Position' => 'nullable',
      'Product_Country' => 'required',
      'Product_Language' => 'required'
    ];
    private $product_validation_messages = [
      'Product_Title.required' => 'Missing title',
      'Product_ID.required' => 'Missing Product ID',
      'Product_Category.required' => 'The product must be assigned to categories',
      'Product_Short_Description.required' => 'Missing Short Description',
      'Product_Description.required' => 'Missing Description',
      'Product_url_key.required' => 'Missing URL key',
      'Product_Is_Visible.required' => 'Missing Is Visible field',
      'Product_Is_Visible.in' => 'Value specified for Is Visible is not valid',
      'Product_Meta_Title.required' => 'Missing Meta Title',
      'Product_Image_Thumbnail.required' => 'Missing Image Thumbnail',
      'Product_Image_Thumbnail.url' => 'The address of thumbnail is not a valid URL',
      'Product_Image.required' => 'Missing Image address',
      'Product_Image.url' => 'The address of Image is not a valid URL',
      'Product_Meta_Description.required' => 'Missing Meta Description',
      'Product_Meta_Noindex.required' => 'Missing Meta_Noindex',
      'Product_Meta_Noindex.in' => 'Value for Meta_NoIndex field is not valid',
      'Product_Delete.in' => 'Value for Product_Delete field is not valid',
      'Product_Price.required' => 'Missing price',
      'Product_Price.numeric' => 'Price must be a numeric value',
      'Product_Price.min' => 'Price must be greater than 0.00',
      'Product_Ext_Link.required' => 'Missing Destination_URL',
      'Product_Ext_Link.url' => 'Destination_URL is not valid',
      'Product_Old_Price.numeric' => 'Old_price must be a numeric value',
      'Product_Position.numeric' => 'The Position inside category must be a numeric value',
      'Product_Country.required' => 'Missing Country',
      'Product_Language.required' => 'Missing Language'
    ];

    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function index(Request $request, $page = 1) {
      if ($request->isXmlHttpRequest()) {
        $pagination_fields = $this->getDataTablesInfoForQuery($request->input());
        $products = Product::paginateForAdmin($pagination_fields);
        $total_products = Product::countForExport();
        return response()->json($this->convertPaginatorToDataTableInput($products, $total_products));
      }

      return view('product/index');

    }

    public function upload(Request $request) {
    
      if (!$request->hasFile('file_to_import')) {
        return new JsonResponse(
          array(
            'status' => 'fail',
            'message' => trans('json.mustSelectFile')
          )
        );
      }

      $file = $request->file('file_to_import');

      if ( strtolower($file->getClientOriginalExtension()) != 'xls' ) {
        return new JsonResponse(
          array(
            'status' => 'fail',
            'message' => trans('json.needValidCSV')
          )
        );
      }

      $disk = \Storage::disk('local');

      if (!$disk->exists('feeds/products')) {
        $disk->makeDirectory('feeds/products');
      }

      $uploaded_date = new \DateTime('now', new \DateTimeZone('UTC'));
      $uploaded_date = $uploaded_date->format('Y-m-d H:i:s');
      $name_without_extension = str_replace('.' . $file->getClientOriginalExtension(), '', $file->getClientOriginalName());
      $new_filename = $name_without_extension . ' - ' . $uploaded_date . '.' . $file->getClientOriginalExtension();

      $file->move(storage_path('app/feeds/products') . DIRECTORY_SEPARATOR, $new_filename);
      $file_path = storage_path('app/feeds/products') . DIRECTORY_SEPARATOR . $new_filename;
      $contents = file_get_contents($file_path);
      if (!mb_detect_encoding($contents, 'UTF-8', true)) {

        try {
          //do nothing
        } 
    
        catch (\Exception $e) {
          throw $e;
        }
  
      }

      return new JsonResponse(
        array(
          'status' => 'success',
          'filename' => $new_filename,
        )
      );
  
    }

    public function import() {
      return view('product/importer',['importType' => 'normal' , 'productHeaderTrans' => trans('product.importer.products_import')]);
    }

    public function importPrices()
    {
        return view('product/importer',['importType' => 'prices' ,'productHeaderTrans' => trans('product.importer.products_import_prices')]);
    }

    public function importStarter(Request $request) {

        Excel::create('Plantilla', function($excel)  {

            $excel->sheet('Products', function ($sheet) {

                $sheet->appendRow($this->required_fields);
            });

        })->export('xls');

    }

    public function export(Request $request) {
      $filename = 'Products-' . date('d-m-Y-H-i-s', time()) ;

      $extended_fields_object = Product::getExtendedFields();
      $extended_fields_array = array();

      foreach ($extended_fields_object as $efo) {
          if ($efo->tag_name != 'product_is_parent' || $efo->tag_name != 0) {
              $extended_fields_array[] = $efo->tag_name;
          }
      }

      $required_fields = array_merge($this->required_fields, $extended_fields_array);
      set_time_limit(0);
        $disk = \Storage::disk('local');

        if (!$disk->exists('exports/products')){
            $disk->makeDirectory('exports/products');
        }

        $handle = fopen(storage_path('app/exports/products/').$filename, 'w');


      $required_fields_to_print = array_map(
    
        function ($v) {
          return utf8_decode($v);
        }
        
        , $required_fields
      );

      $required_as_keys = array_flip($required_fields_to_print);
      $required_as_keys = array_map(
        
        function () {
          return null;
        }
        
        , $required_as_keys
      );
      fputcsv($handle, $required_fields_to_print);
      $export_count = Product::countForExport();
      $limit = 100;
      $chunks = ceil($export_count / $limit);


        Excel::create($filename, function($excel) use($required_fields_to_print,$chunks,$limit,$required_fields,$required_as_keys) {

            $excel->sheet('Products', function($sheet) use ($required_fields_to_print,$chunks,$limit,$required_fields,$required_as_keys){

                $sheet->appendRow($required_fields_to_print);

                for ($i =1; $i <= $chunks; $i++) {

                    $products = Product::forExport($limit, (($i -1) * $limit));

                    foreach ($products as $product) {

                        $cats = explode(',',$product->categories);

                        $resultArray = (array)$product;

                        $resultArray = array_map(
                            function ($value) {
                                return utf8_decode($value);
                            }

                            , $resultArray
                        );

                        $resultArray['Product_Is_Visible'] = $resultArray['Product_Is_Visible'] == 1 ? 'yes' : 'no';
                        $resultArray['Product_Meta_Noindex'] = $resultArray['Product_Meta_Noindex'] == 1 ? 'yes' : 'no';
                        $resultArray['Product_Is_Parent'] = $resultArray['Product_Is_Parent'] == 1 ? 'yes' : 'no';
                        $resultArray['Product_Category'] = implode(';', $cats);
                        $props = is_null($product->props) ? [] : explode('jsonSeparator', $product->props);

                        foreach ($props as $prop) {

                            $prop =json_decode($prop);

                            if (in_array($prop->name, $required_fields)) {
                                $resultArray[$prop->name] = utf8_decode($prop->value);
                            }

                        }

                        unset($resultArray['id']);
                        $resultArray = array_merge($required_as_keys, $resultArray);

                        $resultArray = array_map(

                            function ($value) {
                                return  preg_replace("/\r|\n/", "", $value)  ;
                            }

                            , $resultArray
                        );

                        unset($resultArray['categories']);
                        unset($resultArray['Product_Previous_Price']);
                        unset($resultArray['props']);
                        $sheet->appendRow($resultArray);

                    }


                }

            });
        })->export('xls');


      try {

      } 
      
      catch (\Exception $e) {
        throw $e;
      }

      die ();
    
    }

    public function parseNumber($number) {
      $dot_pos = strpos($number,'.');
      $comma_pos = strpos($number, ',');

      if ($dot_pos !== false && $comma_pos !== false) {

        if ($dot_pos > $comma_pos) {
          $number = str_replace(',', '', $number);
        }

        if ($comma_pos > $dot_pos) {
          $number = str_replace('.', '', $number);
          $number = str_replace(',', '.', $number);
        }
        
        return $number;

      } 
      
      else {
            
        if ($dot_pos !== false) {
          return $number;
        }
        
        if ($comma_pos !== false) {
          return str_replace(',','.', $number);
        }
        
      }

      return $number;
    
    }
    
    public function doImport(Request $request,$type) {
      set_time_limit(0);
      $file_path = storage_path('app/feeds/products') . DIRECTORY_SEPARATOR . urldecode($request->input('file'));

      if (!file_exists($file_path)) {
        return new JsonResponse(
          [
            'status' => 'fail',
            'message' => trans('json.errorOpenFile')
          ]
        );
      }
      $fields = null;
      $error_on_row = false;
      $file_errors = false;
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
      
      if (!is_dir(storage_path('app/feeds/tasks'))) {
        mkdir(storage_path('app/feeds/tasks'), 0777);
      }

      $routing_file_name = 'routing_tasks_'.md5(time()).'.tsk';
      $search_engine_file_name = 'search_engine_'.md5(time()).'.tsk';
      $routing_file_handle = fopen(storage_path('app/feeds/tasks/'.'routing_tasks_'.md5(time()).'.tsk'), 'w');
      $search_engine_file_handle = fopen(storage_path('app/feeds/tasks/'.'search_engine_'.md5(time()).'.tsk'), 'w');


        Excel::filter('chunk')->load($file_path)->chunk(250, function($results) use ($type, $log_file_handle, $error_on_row, &$file_errors, &$totals, $routing_file_handle, $search_engine_file_handle)
        {
                $firstRow = $results->first()->keys()->toArray();

                $results =  $results->toArray();

                $missing_fields = $this->validate_fields_array(array_values($firstRow),$type);

                if (count($missing_fields) > 0) {
                    $missing_fields_error = '';

                    foreach ($missing_fields as $field) {
                        $missing_fields_error .= 'In the CSV file, we cannot find the mandatory field ' . $field . '<br/>';
                    }

                    return new JsonResponse(
                        [
                            'status' => 'fail',
                            'message' => $missing_fields_error
                        ]
                    );
                }

            foreach($results as $row)
            {
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

                $validation_errors = false;

                    try {
                        $value =$row;

                        $value['Product_Price'] = $this->parseNumber($value['Product_Price']);

                        if (isset($value['Product_Old_Price'])) {
                            $value['Product_Old_Price'] = $this->parseNumber($value['Product_Old_Price']);
                        }

                        $validator = \Validator::make($value,($type == 'prices') ? $this->product_prices_validation_rules : $this->product_validation_rules, $this->product_validation_messages);

                        if ($validator->fails()) {
                            $messages_string = implode(', ', $validator->errors()->all());
                            $full_message_string = 'We cannot import the product '.$value['Product_Title'].' due to the following errors: '.$messages_string;
                            fwrite($log_file_handle, $full_message_string . "\r\n");
                            $validation_errors = true;
                            $file_errors = true;
                            throw new \Exception('Invalid data');
                        }



                        if ($type == 'normal'){
                            $localeCounter = Setup::where('country_abre',$value['Product_Country'])->where('language_abre',$value['Product_Language'])->count();
                            if ($localeCounter <= 0) {
                                throw new \Exception('Invalid locale');
                            }
                        }

                    }
                    catch (\Exception $e) {
                        \Log::info('Import error: '.$e->getMessage());
                        $error_on_row = true;
                    }

                    if ($error_on_row) {

                        if (!$validation_errors) {
                            $error_on_row = false;
                            fwrite($log_file_handle, 'Import error: ' . json_encode($row) . "\r\n");
                            $file_errors = true;
                        }

                    }
                    else {
                        \DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
                        if ($type == 'prices')
                        {
                            $product = Product::where('product_id',$value['Product_ID'])->first();
                            if (!is_null($product))
                            {
                                $product->update([
                                    'price' => $value['Product_Price'],
                                    'previous_price' => $value['Product_Old_Price']
                                ]);
                                $totals['updated']['done']++;
                            }

                        }
                        else {

                            if (isset($value['Product_Delete']) && strtolower($value['Product_Delete']) == 'yes') {

                                try {
                                    \DB::beginTransaction();
                                    $totals['removed']['count']++;
                                    $p = \DB::table('products')
                                        ->where('country', $value['Product_Country'])
                                        ->where('language', $value['Product_Language'])
                                        ->where('product_id', '=', $value['Product_ID'])->first();
                                    $pid = $p->id;
                                    \DB::table('virtual_routes')
                                        ->where('route_type', '=', 'p')
                                        ->where('object_id', '=', $pid)
                                        ->delete();
                                    \DB::table('product_views')
                                        ->where('product_id', '=', $pid)
                                        ->delete();
                                   $tags =  \DB::table('product_tags')
                                        ->where('product_id', '=', $pid)
                                        ;

                                    $tagsIds =[] ;

                                    foreach ($tags->select('tag_id')->get() as $tagId)
                                    {
                                        $tagsIds [] = $tagId->tag_id;
                                    }

                                   \DB::table('tags')->whereIn('id',$tagsIds)->delete();

                                    $tags->delete();

                                    \DB::table('product_properties')
                                        ->where('country', $value['Product_Country'])
                                        ->where('language', $value['Product_Language'])
                                        ->where('product_id', '=', $pid)
                                        ->delete();
                                    \DB::table('product_category')
                                        ->where('product_id', '=', $pid)
                                        ->delete();
                                    \DB::table('products')
                                        ->where('country', $value['Product_Country'])
                                        ->where('language', $value['Product_Language'])
                                        ->where('product_id', '=', $value['Product_ID'])
                                        ->delete();
                                    \DB::commit();
                                    fwrite($routing_file_handle, 'remove:'.$pid."\r\n");
                                    fwrite($search_engine_file_handle, 'remove:'.$pid."\r\n");
                                    $totals['removed']['done']++;
                                }
                                catch (\Exception $e) {
                                    \DB::rollBack();
                                    $file_errors = true;
                                    fwrite($log_file_handle, 'Error when deleting ' . $value['Product_Title'] . ' Details: ' . $e->getMessage());
                                }

                            }
                            else {
                                $found = \DB::table('products')
                                    ->select('id')
                                    ->where('country', $value['Product_Country'])
                                    ->where('language', $value['Product_Language'])
                                    ->where('product_id', '=', $value['Product_ID'])
                                    ->get(['id']);

                                if ($found) {


                                    try {
                                        $totals['updated']['count']++;
                                        \DB::beginTransaction();
                                        \DB::table('products')
                                            ->where('country', $value['Product_Country'])
                                            ->where('language', $value['Product_Language'])
                                            ->where('product_id', '=', $value['Product_ID'])
                                            ->update(
                                                array(
                                                    'title' => $this->filtersafe(utf8_encode($value['Product_Title'])),
                                                    'product_id' => $value['Product_ID'],
                                                    'short_description' => $this->filtersafe(utf8_encode($value['Product_Short_Description'])),
                                                    'description' => $this->filtersafe(utf8_encode($value['Product_Description'])),
                                                    'url_key' => $this->slug(lintUrl($value['Product_url_key'])),
                                                    'is_visible' => strtolower($value['Product_Is_Visible']) == 'yes' ? 1 : 0,
                                                    'meta_title' => $this->filtersafe(utf8_encode($value['Product_Meta_Title'])),
                                                    'image' => $value['Product_Image'],
                                                    'thumbnail' => $value['Product_Image_Thumbnail'],
                                                    'meta_description' => $this->filtersafe(utf8_encode($value['Product_Meta_Description'])),
                                                    'meta_index' => strtolower($value['Product_Meta_Noindex']) == 'yes' ? 1 : 0,
                                                    'price' => $value['Product_Price'],
                                                    'destination_url' => $value['Product_Ext_Link'],
                                                    'previous_price' => $value['Product_Old_Price'] ? $value['Product_Old_Price'] : null,
                                                    'brand' => utf8_encode($value['Product_Brand']) ? $this->filtersafe(utf8_encode($value['Product_Brand'])) : null,
                                                    'category_sort' => isset($value['Product_Position']) && $value['Product_Position'] ? $value['Product_Position'] : null,
                                                    'country' => $value['Product_Country'],
                                                    'language' => $value['Product_Language'],
                                                    'parent_id' => isset($value['Product_Parent_ID']) && $value['Product_Parent_ID'] ? $value['Product_Parent_ID'] : null,
                                                    'store' => isset($value['Product_Store']) && $value['Product_Store'] ? $this->filtersafe($value['Product_Store']) : null,
                                                    'image_alt' => isset($value['Product_Image_Alt']) && $value['Product_Image_Alt'] ? $this->filtersafe($value['Product_Image_Alt']) : null,
                                                    'shipping_cost' => isset($value['Product_Shipping_Cost']) && $value['Product_Shipping_Cost'] ? $value['Product_Shipping_Cost'] : null,
                                                    'winner' => isset($value['Product_Shipping_Cost']) && (isset($value['Product_Winner']) &&  strtolower($value['Product_Winner']) == 'yes') ? 1 : 0,
                                                    'stock' => isset($value['Product_Stock']) && strtolower($value['Product_Stock']) ==  1 ? 1 : 0,
                                                    'is_parent' =>  isset($value['Product_Is_Parent']) && strtolower($value['Product_Is_Parent']) == 'yes' ? 1 : 0,
                                                    'parent_filters' => isset($value['Product_Parent_Filters']) && $value['Product_Parent_Filters'] ? $value['Product_Parent_Filters'] : null
                                                )
                                            );
                                        $this->updateProperties($found[0]->id, $value);
                                        $this->updateCategories($found[0]->id, $value['Product_Category']);
                                        \DB::commit();

                                        fwrite($routing_file_handle, 'update:'.$found[0]->id.':'.$value['Product_url_key']."\r\n");
                                        fwrite($search_engine_file_handle, 'update:'.$found[0]->id."\r\n");
                                        $totals['updated']['done']++;
                                    }
                                    catch (\Exception $e) {
                                        \DB::rollBack();
                                        $file_errors = true;
                                        fwrite($log_file_handle, 'Error when updating ' . $value['Product_Title'] . ' Details: ' . $e->getMessage());
                                    }

                                }
                                else {

                                    try {
                                        $totals['added']['count']++;
                                        \DB::beginTransaction();
                                        $inserted_id = \DB::table('products')
                                            ->insertGetId(
                                                array(
                                                    'title' => utf8_encode($value['Product_Title']),
                                                    'product_id' => $value['Product_ID'],
                                                    'short_description' => utf8_encode($value['Product_Short_Description']),
                                                    'description' => utf8_encode($value['Product_Description']),
                                                    'url_key' => lintUrl($value['Product_url_key']),
                                                    'is_visible' => strtolower($value['Product_Is_Visible']) == 'yes' ? 1 : 0,
                                                    'meta_title' => utf8_encode($value['Product_Meta_Title']),
                                                    'image' => $value['Product_Image'],
                                                    'thumbnail' => $value['Product_Image_Thumbnail'],
                                                    'meta_description' => utf8_encode($value['Product_Meta_Description']),
                                                    'meta_index' => strtolower($value['Product_Meta_Noindex']) == 'yes' ? 1 : 0,
                                                    'price' => $value['Product_Price'],
                                                    'destination_url' => $value['Product_Ext_Link'],
                                                    'previous_price' => $value['Product_Old_Price'] ? $value['Product_Old_Price'] : null,
                                                    'brand' => utf8_encode($value['Product_Brand']) ? utf8_encode($value['Product_Brand']) : null,
                                                    'category_sort' => isset($value['Product_Position']) && $value['Product_Position'] ? $value['Product_Position'] : null,
                                                    'country' => $value['Product_Country'],
                                                    'language' => $value['Product_Language'],
                                                    'parent_id' => isset($value['Product_Parent_ID'])  && $value['Product_Parent_ID'] ? $value['Product_Parent_ID'] : null,
                                                    'store' => isset($value['Product_Store']) && $value['Product_Store'] ? $value['Product_Store'] : null,
                                                    'image_alt' => isset($value['Product_Image_Alt']) && $value['Product_Image_Alt'] ? $value['Product_Image_Alt'] : null,
                                                    'shipping_cost' => isset($value['Product_Shipping_Cost']) && $value['Product_Shipping_Cost'] ? $value['Product_Shipping_Cost'] : null,
                                                    'winner' => isset($value['Product_Shipping_Cost']) && (isset($value['Product_Winner']) &&  strtolower($value['Product_Winner']) == 'yes') ? 1 : 0,
                                                    'stock' => isset($value['Product_Stock']) && strtolower($value['Product_Stock']) ==  1 ? 1 : 0,
                                                    'is_parent' =>  isset($value['Product_Is_Parent']) && strtolower($value['Product_Is_Parent']) == 'yes' ? 1 : 0,
                                                    'parent_filters' => isset($value['Product_Parent_Filters']) && $value['Product_Parent_Filters'] ? $value['Product_Parent_Filters'] : null
                                                )
                                            );
                                        $this->updateProperties($inserted_id, $value);
                                        $this->updateCategories($inserted_id, $value['Product_Category']);
                                        \DB::commit();
                                        fwrite($routing_file_handle, 'add:'.$inserted_id.':'.$value['Product_url_key']."\r\n");
                                        fwrite($search_engine_file_handle, 'add:'.$inserted_id."\r\n");
                                        $totals['added']['done']++;
                                    }
                                    catch (\Exception $e) {
                                        \DB::rollBack();
                                        fwrite($log_file_handle, 'Error when creating ' . $value['Product_Title'] . ' Details: ' . $e->getMessage());
                                        $file_errors = true;
                                    }

                                }

                            }
                        }


                    }



            }

        },false);
      //$lexer->parse($file_path, $interpreter);
      fclose($search_engine_file_handle);
      fclose($routing_file_handle);
      $import = new Import();
      $import->user_id = \Auth::getUser()->id;
      $import->type = 'products';
      $import->filename = $request->input('file');
      $import->log_file = $log_file_name;
      $import->routing_task_file = $routing_file_name;
      $import->se_task_file = $search_engine_file_name;
      $import->totals = serialize($totals);
      $import->save();

      if ($file_errors) {
        return new JsonResponse(
          [
            'status' => 'success',
            'message' => trans('json.importedProductsError'),
            'results' => \View::make('product/import_results', ['import' => $import, 'totals' => $totals])->render(),
            'import_id' => $import->id
          ]
        );
      } 
      else {
        return new JsonResponse(
          [
            'status' => 'success',
            'message' => trans('json.importedProductsSuccess'),
            'results' => \View::make('product/import_results', ['import' => $import, 'totals' => $totals])->render(),
            'import_id' => $import->id
          ]
        );
      }
      
    }

    private function validate_fields_array($fields,$type) {
      $required_fields = array(
        'product_title',
        'product_id',
        'product_category',
        'product_short_description',
        'product_description',
        'product_url_key',
        'product_is_visible',
        'product_meta_title',
        'product_image_thumbnail',
        'product_image',
        'product_meta_description',
        'product_meta_noindex',
        'product_delete',
        'product_price',
        'product_ext_link',
        'product_brand',
        'product_old_price',
        'product_country',
        'product_language'
      );
        if ($type == 'prices')
        {
            $required_fields = $this->productPricesFields;
        }

      return array_diff($required_fields, $fields);
    }

    private function updateProperties($product_id, $value) {


      $extra_fields = array_where($value, 
      
        function ($value, $key) {

          if (!starts_with($key, 'Product_') && $value) {

            return true;
          }
            return false;
      
        }
        
      );

      if ($extra_fields) {

        \DB::table('product_tags')->where('product_id', $product_id)->delete();

        foreach($extra_fields as $key=>$value) {
          $tag = Tags::where('tag_name', $key)->where('tag_value', $value)->first();

          if (!$tag) {
            $tag = new Tags();
            $sessionCountry = isset($_COOKIE['sessionCountry']) ? $_COOKIE['sessionCountry'] : '';
            $sessionLanguage = isset($_COOKIE['sessionLanguage']) ? $_COOKIE['sessionLanguage'] : '';
            $currentLocale = get_current_locate($sessionCountry,$sessionLanguage);
            $currentLocale = str_replace('/','',$currentLocale);
            $pieces = explode("-", $currentLocale);
            $tag->country = strtolower($pieces[0]);
            $tag->language = strtolower($pieces[1]);
            $tag->tag_name = $key;
            $tag->tag_value = $value;
            $tag->save();
          }

          $product_tag = new ProductTag();
          $product_tag->product_id = $product_id;
          $product_tag->tag_id = $tag->id;
          $product_tag->save();

        }

      }
    }

    private function updateCategories($product_id, $categories) {
      $statement = 'DELETE FROM product_category WHERE product_id = ' . $product_id;
      \DB::statement($statement);
      \DB::statement('SET FOREIGN_KEY_CHECKS = 0');
      $categories = explode(';', $categories);

      $categories = array_map(
      
        function ($v) {
          
          try {
            $v = intval(trim($v));
            
            if ($v > 0) {
              return $v;
            } 
            
            else {
              return null;
            }
          
          } 
          
          catch (\Exception $e) {
            return null;
          }
        }
        
        , $categories
      );
      $categories = array_unique($categories);
      $data = [];

      foreach ($categories as $cat) {
        
        if ($cat) {
          $data[] = array(
            'product_id' => $product_id,
            'category_id' => $cat,
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
          );
        }
        
      }

      \DB::table('product_category')->insert($data);
    }

    public function updateRoutesAction(Request $request) {
    
      try {        
        set_time_limit(0);
        \Log::info('Starting updateRoutesAction');
        
        if (!$request->has('import_id') || !$request->input('import_id')) {
          app('MainService')->storeProductRoutes();
        } 
        
        else {                  
          app('MainService')->handleImportRoutes($request->input('import_id'));
        }
            
        \Log::info('Finishing updateRoutesAction');
        return new JsonResponse(
          [
            'status' => 'success',
            'message' => trans('json.updatedRoutesSuccess')
          ]
        );

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

    }

    public function updateCategoriesRoutesAction(Request $request) {

      try {
        set_time_limit(0);
        app('MainService')->storeCategoryRoutes($request->input('language'),$request->input('country'));
        return new JsonResponse(
          [
            'status' => 'success',
            'message' => trans('json.updatedRoutesSuccess')
          ]
        );
      } 
      
      catch (\Exception $e) {
        return new JsonResponse(
          [
            'status' => 'fail',
            'message' => trans('json.errorUpdatingRoutes')
          ]
        );
      }

    }

    public function editForm(Request $request, $id) {
      $product = Product::find($id);
      $brands = Brand::where('country',get_current_country())->where('language',get_current_language())->get();
      $stores = Store::where('country',get_current_country())->where('language',get_current_language())->get();

      if (!$product) {
        \Session::flash('error', trans('flash.error.product_not_found') );
        return redirect(route('admin_products_list'));
      }

      $categories = Category::where('country',get_current_country())->where('language',get_current_language())->get();
      $setups = Setup::all();
      $possibleParents = Product::where('id', '!=', $id)->whereNull('parent_id')->where('country', get_current_country())->where('language', get_current_language())->get();
      return view('product/form', array('product' => $product, 'categories' => $categories, 'setups' => $setups, 'possibleParents' => $possibleParents , 'brands' => $brands ,'stores' => $stores));
    }

    public function editFormProcess(Requests\ProductFormRequest $request, $id) {
      
      try {
      
        $setup = Setup::find($request->input('product_country')); 
        
        $product = Product::find($id);
        $needs_re_routing = $product->url_key != $request->input('product_url');
        $product->title = $request->input('product_title');
        $product->url_key = addslashes($request->input('product_url'));
        $product->product_id = $request->input('product_id');
        $product->short_description = $request->input('product_short_description');
        $product->description = $request->input('product_description');
        $product->is_visible = $request->input('product_visible') == '1' ? true : false;
        $product->image = $request->input('product_image') ? $request->input('product_image') : null;
        $product->thumbnail = $request->input('product_thumbnail') ? $request->input('product_thumbnail') : null;
        $product->price = $request->input('product_price');
        $product->previous_price = $request->input('Product_Old_Price') ? $request->input('Product_Old_Price') : null;
        $product->brand = $request->input('product_brand') ? $request->input('product_brand') : null;
        $product->meta_title = $request->input('product_meta_title');
        $product->meta_description = $request->input('product_meta_description');
        $product->meta_index = $request->input('product_meta_noindex') == '1' ? true : false;
        $product->parent_id = empty($request->input('product_parent_id')) ? null : $request->input('product_parent_id');
        $product->country = $setup->country_abre;
        $product->language = $setup->language_abre;
        $product->winner = $request->input('product_winner') == '1' ? true : false;
        $product->stock = $request->input('product_stock') == '1' ? true : false;
        $product->store = $request->input('product_store');
        $product->image_alt = $request->input('product_image_alt');
        $product->shipping_cost = $request->input('product_shipping_cost');
        $product->parent_filters = $request->input('product_parent_filters');
        $product->save();
        $statement = 'DELETE FROM product_category WHERE product_id = ' . $product->id;
        \DB::statement($statement);

        foreach ($request->input('product_category') as $pc) {
          $product_category = new ProductCategory();
          $product_category->product_id = $product->id;
          $product_category->category_id = $pc;
          $product_category->save();
        }

        $app = \App::getFacadeRoot();
        $service = $app['MainService'];
        
        if ($needs_re_routing) {
          $service->generateProductRoutes();
        }

        try {
          $service->updateProductInElasticsearchIndex(app('ESClient'), $product);
        } 
        
        catch (\Exception $e) {
          
          try {
            $service->updateFullElasticsearchIndex();
          } 
          
          catch (\Exception $e) {
            \Log::error($e->getMessage(), array('context' => 'elasticsearch', 'trace' => $e->getTraceAsString()));
            \Session::flash('error', trans('flash.error.index_not_updated') . '<a href="' . route('search_engine_index') . '">'. trans('flash.error.here'). '</a> ');
            return redirect(route('admin_product_list'));
          }
        
        }
        \Session::flash('success', trans('flash.success.operation_done_successfully') ) ;
        return redirect(route('admin_product_list'));
      } 
      
      catch (\Exception $e) {
        throw $e;
      }
    
    }

    public function batchAction(Request $request) {
      
      if (!$request->isXmlHttpRequest()) {
        abort(404);
      }

      if ($request->get('action') == 'remove') {
        $ids = $request->input('ids', null);

        if ($ids) {
        
          try {
            
            foreach ($ids as $id) {
              \DB::table('virtual_routes')
                ->where('route_type', '=', 'p')
                ->where('object_id', '=', $id)
                ->delete();
              \DB::table('product_views')
                ->where('product_id', '=', $id)
                ->delete();
              \DB::table('product_tags')
                ->where('product_id', '=', $id)
                ->delete();
              \DB::table('product_properties')
                ->where('country', get_current_country())
                ->where('language', get_current_language())
                ->where('product_id', '=', $id)
                ->delete();
              \DB::table('product_category')
                ->where('product_id', '=', $id)
                ->delete();
              \DB::table('products')->delete($id);
            }

            return response()->json(
              [
                'status' => 'success',
                'message' => trans('json.productsRemovedSuccess')
              ]
            );

          } 
          
          catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(
              [
                'status' => 'fail',
                'message' => trans('json.errorRemovingProducts')
              ]
            );

          }

        }
        return response()->json(
          [
            'status' => 'fail',
            'message' => trans('json.mustSelectProducts')
          ]
        );
      }
      
      if ($request->get('action') == 'toggle') {
        $ids = $request->input('ids', null);
        
        if ($ids) {
          
          try {
            foreach ($ids as $id) {
              $product = Product::find($id);
              
              if ($product) {
                $product->is_visible = $product->is_visible == true ? false : true;
                $product->save();
              }
            
            }

            return response()->json(
              [
                'status' => 'success',
                'message' => trans('json.importedProductsSuccess')
              ]
            );

          } 
          
          catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(
              [
                'status' => 'fail',
                'message' => trans('json.errorTogglingProducts')
              ]
            );
          }
        }

        return response()->json(
          [
            'status' => 'fail',
            'message' => trans('json.mustSelectProducts')
          ]
        );

      }
    }

    public function integrityCheckAction(Request $request) {
      $grouped_dupes = Product::findDupes();
      $dupes = [];

      foreach ($grouped_dupes as $gd) {
        $sd = \DB::table('products')->where('country', get_current_country() )->where('language', get_current_language() )->whereRaw(\DB::raw('REPLACE(url_key, \'/\', \'\') = "' . $gd->rep_url . '"'))->get();

        foreach ($sd as $single_dupe) {
          $dupes[] = $single_dupe;
        }

      }
      
      $products_without_categories = Product::whereDoesntHave('categories', 
      
        function ($query) {

        }
      
      )
        ->where('country', get_current_country() )
        ->where('language', get_current_language())
        ->get();
      $products_with_missing_fields = Product::findWithMissingFields();
      return view('product/integrity_check', ['dupes' => $dupes, 'without_categories' => $products_without_categories, 'with_missing_fields' => $products_with_missing_fields]);
    }
    
    public function slug($string) {
      return strtolower(trim(preg_replace('~[^0-9a-z/]+~i', '-', html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), '-'));
    }

    public function filtersafe($string) {
      // Replace Single Curly Quotes
      $search[]  = chr(226).chr(128).chr(152);
      $replace[] = "'";
      $search[]  = chr(226).chr(128).chr(153);
      $replace[] = "'";
      // Replace Smart Double Curly Quotes
      $search[]  = chr(226).chr(128).chr(156);
      $replace[] = '"';
      $search[]  = chr(226).chr(128).chr(157);
      $replace[] = '"';
      // Replace En Dash
      $search[]  = chr(226).chr(128).chr(147);
      $replace[] = '--';
      // Replace Em Dash
      $search[]  = chr(226).chr(128).chr(148);
      $replace[] = '---';
      // Replace Bullet
      $search[]  = chr(226).chr(128).chr(162);
      $replace[] = '*';
      // Replace Middle Dot
      $search[]  = chr(194).chr(183);
      $replace[] = '*';
      // Replace Ellipsis with three consecutive dots
      $search[]  = chr(226).chr(128).chr(166);
      $replace[] = '...';
      // Apply Replacements
      $string = str_replace($search, $replace, $string);
      // Remove any non-ASCII Characters
      $string = preg_replace("/[^\x01-\x7F]/","", $string);
      return $string;     
    }
    
    public function getChildrenAction(Request $request) {
      $children_products = null;
      $parent_filters = [];
      $parent_filters_values = [];
      $children_products = Product::where('country', get_current_country())
        ->where('language', get_current_language())
        ->where('parent_id',intval($request->input('product_id')))
        ->get();
      foreach ($children_products as &$children_product) {
        $aPFilter = explode(";", $children_product->parent_filters);
        if ($children_product->previous_price) {
          $children_product->previous_price = print_price($children_product->previous_price);
        }
        $children_product->price = print_price($children_product->price);
        foreach ($aPFilter as $aFilter) {
          if (in_array($aFilter, $parent_filters)) {
            //do nothing
          }
          else {
            array_push($parent_filters, $aFilter);
          }
          $parent_filters_values[$aFilter . '_' . strval($children_product->id)] =  ucfirst(getTagForProduct($children_product->id, $aFilter));
        }
      }
      return response()->json(array('children_products'=>$children_products, 'parent_filters'=>$parent_filters, 'parent_filters_values'=>$parent_filters_values));
    }

  }