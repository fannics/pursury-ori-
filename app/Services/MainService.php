<?php

namespace ProjectCarrasco\Services;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use ProjectCarrasco\Category;
use ProjectCarrasco\Brand;
use ProjectCarrasco\Store;
use ProjectCarrasco\ColorCodes;
use ProjectCarrasco\Import;
use ProjectCarrasco\MenuConfiguration;
use ProjectCarrasco\Paginator\AppPaginator;
use ProjectCarrasco\Product;
use ProjectCarrasco\ProductView;
use ProjectCarrasco\TermSearch;
use ProjectCarrasco\User;
use ProjectCarrasco\VirtualRouting;
use Symfony\Component\Yaml\Yaml;

class MainService
{

    function __construct()
    {

    }

    public function generateCategoryRoutes(){

        set_time_limit(0);

        $categories = Category::where('country',get_current_country())->where('language',get_current_language())->get();

        $storage = storage_path('app/category_routes.php');

        $handle = fopen($storage, 'w');

        fwrite($handle, '<?php'.PHP_EOL);

        foreach($categories as $cat){

            $url = starts_with($cat->url_key, '/') ? $cat->url_key : '/'.$cat->url_key;

            $url = ends_with($cat->url_key, '/') ? $url : $url.'/';

            $sessionCountry = isset($_COOKIE['sessionCountry']) ? $_COOKIE['sessionCountry'] : '';
            $sessionLanguage = isset($_COOKIE['sessionLanguage']) ? $_COOKIE['sessionLanguage'] : '';
            fwrite($handle, 'Route::get("'.\Config::get('app')['route_prefix']. $sessionCountry.$sessionLanguage.$url.'{page?}", array(\'as\'=> \'category_'.$cat->id.'_route'.'\', \'uses\' => \'MainController@categoryPage\', \'middleware\' => \'id_inserter\', \'ref_id\' => '.$cat->id.'))->where(\'page\', \'[0-9]+\');'.PHP_EOL);
        }

        fclose($handle);

    }

    public function generateProductRoutes(){

        set_time_limit(0);

        $products_to_update = Product::with('categories')->where('country', get_current_country())->where('language', get_current_language())->visible()->count();

        $chunks = ceil($products_to_update / 100);

        $amount = 0;

        try{

            $storage = storage_path('app/product_routes.php');
                                                                         
            $handle = fopen($storage, 'w');

            fwrite($handle, '<?php'.PHP_EOL);

            for ($i =1; $i <= $chunks; $i++){

                $products_to_update = Product::with('categories')->where('country', get_current_country())->where('language', get_current_language())->visible()->take(100)->offset((($i -1) * 100))->get();

                foreach($products_to_update as $prod){

                    $url = starts_with($prod->url_key, '/') ? $prod->url_key : '/'.$prod->url_key;

                    $sessionCountry = isset($_COOKIE['sessionCountry']) ? $_COOKIE['sessionCountry'] : '';
                    $sessionLanguage = isset($_COOKIE['sessionLanguage']) ? $_COOKIE['sessionLanguage'] : '';
                    fwrite($handle, 'Route::get("'.\Config::get('app')['route_prefix']. $sessionCountry.$sessionLanguage.$url.'", array('.(!$prod->is_visible ? '\'middleware\' => \'is_admin\', ' : '').'\'as\'=> \'product_'.$prod->id.'_route'.'\', \'uses\' => \'MainController@productPage\', \'middleware\' => \'id_inserter\', \'ref_id\' => '.$prod->id.'));'.PHP_EOL);

                }

                unset($products_to_update);



            }

            fclose($handle);

        } catch (\Exception $e){

            return array('status' => 'fail', 'message' => $e->getMessage());

        }
    }

    public function storeProductRoutes($language = null , $country = null){

        $language = ( $language == null ? get_current_language() : $language);
        $country = ($country == null ? get_current_country() : $country);

        \Log::info('Updating productRoutes');

        \DB::table('virtual_routes')->where('country', $country )->where('language',$language )->where('route_type', 'p')->delete();

        $products_to_update = Product::visible()->where('country', $country )->where('language', $language )->count();

        $chunks = ceil($products_to_update / 500);

        try {

            for ($i = 1; $i <= $chunks; $i++) {

                $products_to_update = \DB::table('products')
                    ->select('products.id', 'products.url_key', 'products.country', 'products.language')
                    ->where('country', $country )
                    ->where('language', $language )
                    ->where('is_visible', true)
                    ->take(500)                                                                  
                    ->offset((($i - 1) * 500))
                    ->get();


                foreach ($products_to_update as $prod) {

                    $url = starts_with($prod->url_key, '/') ? $prod->url_key : '/' . $prod->url_key;
                    
                    $url_r_ = $url;
	                  $sessionCountry = isset($_COOKIE['sessionCountry']) ? $_COOKIE['sessionCountry'] : '';
                    $sessionLanguage = isset($_COOKIE['sessionLanguage']) ? $_COOKIE['sessionLanguage'] : '';
                    if (starts_with($url_r_, $sessionCountry.$sessionLanguage)) {
                      $url_r_ = str_replace( $sessionCountry.$sessionLanguage, '', $url_r_);
		                }

                    try {
                      \DB::table('virtual_routes')->insert([
                        'route_type' => 'p',
                        'route' => $url_r_,
                        'object_id' => $prod->id,
                        'country' => $prod->country,
                        'language' => $prod->language
                      ]);
                    }
                    catch (\Exception $e) {
                      \Log::error($e->getMessage());
                    }

                }

                unset($products_to_update);

            }
        } catch (\Exception $e){
            \Log::error($e->getMessage());
            throw $e;
        }
    }

    public function handleImportRoutes($import_id){

        $import = Import::find($import_id);

        $filename = storage_path('/app/feeds/tasks/'.$import->routing_task_file);

        if (file_exists($filename)){

            $handle = fopen($filename, 'r');

            while(!feof($handle)){
                $command = fgets($handle);

                $command_parts = explode(':', $command);

                switch($command_parts[0]){
                    case 'add':
                    case 'update':

                        $vr = \DB::table('virtual_routes')->where('route_type', 'p')->where('object_id', $command_parts[1])->first(['id']);

                        $url_r_ = $command_parts[2];
	                      $sessionCountry = isset($_COOKIE['sessionCountry']) ? $_COOKIE['sessionCountry'] : '';
                        $sessionLanguage = isset($_COOKIE['sessionLanguage']) ? $_COOKIE['sessionLanguage'] : '';
                        if (starts_with($url_r_, $sessionCountry.$sessionLanguage)) {
                          $url_r_ = str_replace( $sessionCountry.$sessionLanguage, '', $url_r_);
		                    }
                        $url_r_ = preg_replace( "/\r|\n/", "", $url_r_);
                        $url_r_ = trim($url_r_);
                        
                        $theProduct = Product::find($command_parts[1]);  
                        if ($vr){
                            \DB::table('virtual_routes')->where('id', $vr->id)->update([
                                'route' => $url_r_,
                                'object_id' => $command_parts[1],
                                'route_type' => 'p',
                                'country' => $theProduct->country,
                                'language' => $theProduct->language                           
                            ]);
                        } 
                        else {
                            \DB::table('virtual_routes')->insert([
                                'route' => $url_r_,
                                'object_id' => $command_parts[1],
                                'route_type' => 'p',
                                'country' => $theProduct->country,
                                'language' => $theProduct->language                           
                            ]);
                        }

                        break;
                    case 'delete':

                        $vr = \DB::table('virtual_routes')->where('route_type', 'p')->where('object_id', $command_parts[1])->delete();

                        break;
                }
            }

            fclose($handle);
            @unlink($filename);

        } else {
            throw new \Exception('The routing task file from the import does not exists');
        }
    }

    private function performChunkSearchEngineUpdateBasedOnIdsList($action, $ids) {
      $products_to_update = \DB::table('products')
        ->where('country', get_current_country() )
        ->where('language', get_current_language() )
        ->whereIn('products.id', $ids)
        ->get(
          [
            'products.id', 
            'products.title', 
            'products.short_description', 
            'products.url_key', 
            'products.price', 
            'products.brand', 
            'products.popularity',
            'products.country',
            'products.language'
          ]
        );
        $this->batchUpdateProductsInElasticsearchIndex(app('ESClient'), $products_to_update);
    }

    public function handleImportSearchEngineUpdate($import_id) {
      $import = Import::find($import_id);
      $filename = storage_path('/app/feeds/tasks/'.$import->se_task_file);
      
      if (file_exists($filename)) {
        $handle = fopen($filename, 'r');
        $upsert = [];
        $remove = [];
        $upsert_count = 0;
        $remove_count = 0;

        while (!feof($handle)) {
          $command = fgets($handle);
          $command_parts = explode(':', $command);

          switch($command_parts[0]) {
            case 'add':
            case 'update';
              $upsert[] = $command_parts[1];
              $upsert_count++;
            break;
            case 'delete':
              $remove[] = $command_parts[1];
              $remove_count++;
            break;
          }

          if ($upsert_count == 10) {
            $this->performChunkSearchEngineUpdateBasedOnIdsList('update', $upsert);
            unset($upsert);
            $upsert_count = 0;
          }

          if ($remove_count == 10) {
            $this->performChunkSearchEngineUpdateBasedOnIdsList('remove', $remove);
            unset($remove);
            $remove_count = 0;
          }

        }

        if (count($upsert) > 0) {
          $this->performChunkSearchEngineUpdateBasedOnIdsList('update', $upsert);
        }

        if (count($remove)) {
          $this->performChunkSearchEngineUpdateBasedOnIdsList('remove', $remove);
        }

        fclose($handle);
        @unlink($filename);

      } 
      
      else {
        throw new \Exception('The search engine task file from the import does not exists');
      }
    
    }

    public function storeCategoryRoutes($language = null , $country = null){
       $language = ( $language == null ? get_current_language() : $language);
        $country = ($country == null ? get_current_country() : $country);

        \DB::table('virtual_routes')->where('country',$country)->where('language',$language)->where('route_type', 'c')->delete();

        $categories_count = Category::visible()->where('country',$country)->where('language',$language)->count();

        $chunks = ceil($categories_count/ 500);

        try {                        

            for ($i = 1; $i <= $chunks; $i++) {

                $categories_chunk = \DB::table('categories')
                    ->select('categories.id', 'categories.url_key','categories.language','categories.country')
                    ->where('is_visible', true)
                    ->where('country', $country )
                    ->where('language', $language )
                    ->take(500)
                    ->offset((($i - 1) * 500))
                    ->get();


                foreach ($categories_chunk as $cat) {

                    $url = starts_with($cat->url_key, '/') ? $cat->url_key : '/' . $cat->url_key;

                    $url_r_ = $url;
	                  $sessionCountry = isset($_COOKIE['sessionCountry']) ? $_COOKIE['sessionCountry'] : '';
                    $sessionLanguage = isset($_COOKIE['sessionLanguage']) ? $_COOKIE['sessionLanguage'] : '';
                    if (starts_with($url_r_, $sessionCountry.$sessionLanguage)) {
                      $url_r_ = str_replace( $sessionCountry.$sessionLanguage, '', $url_r_);
		                }

                    \DB::table('virtual_routes')->insert([
                        'route_type' => 'c',
                        'route' => $url_r_,
                        'object_id' => $cat->id,
                        'country' => $cat->country,
                        'language' => $cat->language
                    ]);

                }

                unset($categories_chunk);

            }
        } catch (\Exception $e){
            dd($e);
            throw $e;
        }
    }

    public function updateBrandsVirtualRoutes($language=null,$country=null){
        $language = ( $language == null ? get_current_language() : $language);
        $country = ($country == null ? get_current_country() : $country);

        \DB::table('virtual_routes')->where('country',$country)->where('language',$language)->where('route_type', 'b')->delete();

        $brandsCount = Brand::where('country',$country)->where('language',$language)->count();

        $chunks = ceil($brandsCount/ 500);

        try {

            for ($i = 1; $i <= $chunks; $i++) {

                $brands_chunks = \DB::table('brands')
                    ->select('brands.id', 'brands.url_key','brands.language','brands.country')
                    ->where('is_visible', true)
                    ->where('country', $country )
                    ->where('language', $language )
                    ->take(500)
                    ->offset((($i - 1) * 500))
                    ->get();


                foreach ($brands_chunks as $brand) {

                    $url = starts_with($brand->url_key, '/') ? $brand->url_key : '/' . $brand->url_key;

                    $url_r_ = $url;
                    $sessionCountry = isset($_COOKIE['sessionCountry']) ? $_COOKIE['sessionCountry'] : '';
                    $sessionLanguage = isset($_COOKIE['sessionLanguage']) ? $_COOKIE['sessionLanguage'] : '';
                    if (starts_with($url_r_, $sessionCountry.$sessionLanguage)) {
                        $url_r_ = str_replace( $sessionCountry.$sessionLanguage, '', $url_r_);
                    }

                    \DB::table('virtual_routes')->insert([
                        'route_type' => 'b',
                        'route' => $url_r_,
                        'object_id' => $brand->id,
                        'country' => $brand->country,
                        'language' => $brand->language
                    ]);

                }

                unset($brands_chunks);

            }
        } catch (\Exception $e){
            dd($e);
            throw $e;
        }
    }

    public function updateStoresVirtualRoutes($language=null , $country = null){

        $language = ( $language == null ? get_current_language() : $language);
        $country = ($country == null ? get_current_country() : $country);

        \DB::table('virtual_routes')->where('country',$country)->where('language',$language)->where('route_type', 's')->delete();

        $storesCount = Store::where('country',$country)->where('language',$language)->count();

        $chunks = ceil($storesCount/ 500);

        try {

            for ($i = 1; $i <= $chunks; $i++) {

                $stores_chunk = \DB::table('stores')
                    ->select('stores.id', 'stores.url_key','stores.language','stores.country')
                    ->where('is_visible', true)
                    ->where('country', $country )
                    ->where('language', $language )
                    ->take(500)
                    ->offset((($i - 1) * 500))
                    ->get();


                foreach ($stores_chunk as $store) {

                    $url = starts_with($store->url_key, '/') ? $store->url_key : '/' . $store->url_key;

                    $url_r_ = $url;
                    $sessionCountry = isset($_COOKIE['sessionCountry']) ? $_COOKIE['sessionCountry'] : '';
                    $sessionLanguage = isset($_COOKIE['sessionLanguage']) ? $_COOKIE['sessionLanguage'] : '';
                    if (starts_with($url_r_, $sessionCountry.$sessionLanguage)) {
                        $url_r_ = str_replace( $sessionCountry.$sessionLanguage, '', $url_r_);
                    }

                    \DB::table('virtual_routes')->insert([
                        'route_type' => 's',
                        'route' => $url_r_,
                        'object_id' => $store->id,
                        'country' => $store->country,
                        'language' => $store->language
                    ]);

                }

                unset($stores_chunk);

            }
        } catch (\Exception $e){
            dd($e);
            throw $e;
        }
    }

    public function createProductView($request, $product){

        $product_view = new ProductView();
        $product_view->product_id = $product->getAttribute('id');
        $product_view->ip_address = $request->getClientIp();
        $product_view->date_of_view = date('Y-m-d H:i:s', time());

        //get the popularity to update the product
        $product_view->save();

        $product->hits = $product->hits != null ? $product->hits + 1 : 1;

        $product->popularity = ($product->hits + $product->shop_visits) / 2;

        $product->save();

        return $product_view;
    }

    public function registerProductView(Request $request, $product){

        try{

            //find if the product has already been seen by the curren client
            $view = ProductView::query()
                ->where('product_id', $product->getAttribute('id'))
                ->where('ip_address', $request->getClientIp())
                ->firstOrFail();

            $current_time = new \DateTime('now', new \DateTimeZone('UTC'));

            $date_of_view = \DateTime::createFromFormat('Y-m-d H:i:s', $view->date_of_view);

            $diff = round(($current_time->getTimestamp() - $date_of_view->getTimestamp()) / 3600);

            if ($diff < 5){
                return $view;
            } else {
                return $this->createProductView($request, $product);
            }

        } catch (ModelNotFoundException $e){

            return $this->createProductView($request, $product);

        }
    }

    public function extendedSearch($term, $page = 1, $limit = 10, $sorting_field = 'popularity', $sorting_direction = 'DESC') {
      
      try {
        
        if (!app('ESClient')) {
          throw new \Exception('Elasticsearch is not working');
        }

        $products = $this->extendedSearchUsingElasticsearch($term, $page, $limit, $sorting_field, $sorting_direction);
        $results = array();

        if ($products['hits']['total'] > 0) {
          $ids = array();

          foreach($products['hits']['hits'] as $product) {
            $ids[] = $product['_id'];
          }

          $results = Product::getProductListFromIdsList($ids, $products);
        }

        $this->registerTermSearch($term, $products['hits']['total']);
        return new AppPaginator(
          $results,
          $products['hits']['total'],
          $limit,
          $page
        );

      } 
      catch (\Exception $e) {
        try {
          $total = Product::findTotalByTerm($term);
          $products = Product::findByTerm($term, $page, $limit, $sorting_field, $sorting_direction);
          $this->registerTermSearch($term, $total);
          return new AppPaginator(
            $products,
            $total,
            $limit,
            $page
          );
        } 
        catch(\Exception $e) {
          return new AppPaginator(
            array(),
            0,
            $limit,
            $page
          );
        }
      }
    }

    public function searchTerm($term){

        $results = array();

        try{

            if (app('ESClient')) {
              $results  = $this->searchTermUsingElasticsearch($term);
            } 
            else {
                throw new \Exception('Elasticsearch is not working');
            }               
        } 
        catch (\Exception $e) {
            $results = \DB::select('
                SELECT title as rec_name, \'product\' as rec_type, url_key as url, thumbnail as thumb
                FROM products
                WHERE is_visible = TRUE
                AND country="'.get_current_country().'"
                AND language="'.get_current_language().'"
                AND (title LIKE "%'.$term.'%" OR description LIKE "'.$term.'")
                AND is_parent IS NOT TRUE
                ORDER BY title, description ASC LIMIT 0, 10'
            );
        }

        $results = json_decode(json_encode($results), true);

        $results = array_map(
        
          function($v) use ($term) {
            $v['url'] = route('main_search', array('page' => null, 'term' => $term));
            $v['thumbnail'] = resized_image($v['thumb'], 'thumbnail', '60x60');
            return $v;
          }, $results

        );
        return $results;
    }

    public function updateProductParentFlags () {
      $affected = \DB::table('products')->update(array('is_parent' => false));
      $theParents =  \DB::table('products')
        ->select('parent_id', 'country', 'language')
        ->groupBy('parent_id', 'country', 'language')
        ->get(); 
      
      foreach ($theParents as $aParent) {
        
        if ( !empty($aParent->parent_id) ) {
          $thisProduct = Product::where('product_id', $aParent->parent_id)->where('country', $aParent->country)->where('language', $aParent->language)->first();
          
          if ($thisProduct) { 
            $thisProduct->is_parent = true;
            $thisProduct->save();
          }
                                              
        }
      
      }
            
    }

    //To update a single product on the elasticsearch index
    public function updateProductInElasticsearchIndex($es_client, $product) {

        $categories = $product->categories;

        $cat_names = [];
        foreach($categories as $cat){
            $cat_names[] = $cat->title;
        }

        $params = [
            'index' => settings('app.elasticsearch_index_name'),
            'type' => 'product',
            'id' => $product->id,
            'body' => [
                'doc' => [
                    'title' => $product->title,
                    'description' => $product->short_description,
                    'url_key' => $product->url_key,
                    'price' => $product->price,
                    'category_title' => implode(', ', $cat_names),
                    'brand' => $product->brand,
                    'popularity' => ($product->hits + $product->shop_visits) / 2, 
                    'country' => $product->country, 
                    'language' => $product->language, 
                ]
            ]
        ];
        $this->updateProductParentFlags();
        return $es_client->update($params);
    }

    //To Bulk update products in the elasticsearch index
    public function batchUpdateProductsInElasticsearchIndex($es_client, $products){

        $index_params = [
            '_index' => settings('app.elasticsearch_index_name'),
            '_type' => 'product',
        ];

        $counter = 1;
        $batch = [];

        foreach($products as $product){

            $product =  (array) $product;    
            
            $index_params['_id'] = $product['id'];

            unset($product['id']);

            $element_params = $product;
            $batch['body'][] = ['index' => $index_params];
            $batch['body'][] = $element_params;

        }
        if (count($batch) > 0){
          $es_client->bulk($batch);
        }
        $this->updateProductParentFlags();
        return true;
    }

    public function updateFullElasticsearchIndex() {
        set_time_limit(0);
        $products_to_update = \DB::table('products')
          ->join('product_category', 'products.id', '=', 'product_category.product_id' )
          ->join('categories', 'product_category.category_id', '=', 'categories.id')
          ->where('products.is_visible', true)->where('categories.is_visible', true)
          ->count();
        $chunks = ceil($products_to_update / 3500);
        $amount = 0;
        
        try {
          
          for ($i =1; $i <= $chunks; $i++) {
            $products_to_update = \DB::table('products')
              ->join('product_category', 'products.id', '=', 'product_category.product_id' )
              ->join('categories', 'product_category.category_id', '=', 'categories.id')
              ->where('products.is_visible', true)->where('categories.is_visible', true)
              ->take(3500)->offset((($i - 1) * 3500))
              ->get(['products.id', 'products.title', 'products.short_description', 'products.url_key', 'products.price', 'products.brand', 'products.popularity','products.country','products.language']);
            $products_to_update = json_decode(json_encode($products_to_update), true);
            $this->batchUpdateProductsInElasticsearchIndex(app('ESClient'), $products_to_update);
          }

        } 
        
        catch (\Exception $e) {
          return array('status' => 'fail', 'message' => $e->getMessage());
        }
        return true;
    }

    public function deleteProductFromElasticsearchIndex($es_client, $product){

        $params = [
            'index' => settings('app.elasticsearch_index_name'),
            'type' => 'product',
            'id' => $product->id
        ];

        $res = $es_client->delete($params);

        return;
    }

    public function updateProductMappingOnElasticsearch(Client $es_client){

        //clean the index

        if($es_client->indices()->exists(['index' => settings('app.elasticsearch_index_name')])){

            $delete_index_params = [
                'index' => settings('app.elasticsearch_index_name')
            ];

            $es_client->indices()->delete($delete_index_params);

        }

        $mapping_info = [
            'index' => settings('app.elasticsearch_index_name'),
            'body' => [
                'settings' => [
                    'analysis' => [
                        'filter' => [
                            'edgeNGramFilter' => [
                                'type' => 'edgeNGram',
                                'min_gram' => 2,
                                'max_gram' => 12
                            ]
                        ],
                        'analyzer' => [
                            'edgeNGramAnalyzer' => [
                                'type' => 'custom',
                                'tokenizer' => 'whitespace',
                                'filter' => [
                                    'lowercase',
                                    'asciifolding',
                                    'edgeNGramFilter'
                                ]
                            ]
                        ]
                    ]
                ],
                'mappings' => [
                    'product' => [
                        '_all' => [
                            'enabled' => true,
                            'search_analyzer' => 'standard',
                            'index_analyzer' => 'edgeNGramAnalyzer',
                        ],
                        '_source' => [
                            'enabled' => true
                        ],
                        'properties' => [
                            'title' => [
                                'type' => 'string',
                                'index' => 'analyzed',
                                'analyzer' => 'spanish',
                                'include_in_all' => true,
                                'fields' => [
                                    'raw' => [
                                        'type' => 'string',
                                        'index' => 'not_analyzed'
                                    ]
                                ]
                            ],
                            'description' => [
                                'type' => 'string',
                                'index' => 'analyzed',
                                'analyzer' => 'spanish',
                                'include_in_all' => true
                            ],
                            'url_key' => [
                                'type' => 'string',
                                'index' => 'no'
                            ],
                            'price' => [
                                'type' => 'float'
                            ],
                            'brand' => [
                                'type' => 'string',
                                'index' => 'analyzed',
                                'analyzer' => 'spanish',
                                'include_in_all' => true
                            ],
                            'popularity' => [
                                'type' => 'float'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = $es_client->indices()->create($mapping_info);

        return $response;

    }

    public function extendedSearchUsingElasticsearch($term, $page, $limit, $sort_field, $sort_direction) {
      $es_client = app('ESClient');
      $sort_field = $sort_field == 'title' ? 'title.raw' : $sort_field;
      $query = [
        'index' => settings('app.elasticsearch_index_name'),
        'type' => 'product',
        'body' => [
          'query' => [
            'bool' => [
              'should' => [
                [
                  'term' => [
                  'country' => get_current_country(),
                  ]
                ],
                [
                  'term' => [
                    'language' => get_current_language(),
                  ]
                ],
                [
                  'term' => [
                    '_all'  => $term
                  ]
                ]
              ],
              "minimum_should_match" => 3
            ]            
          ],
          'size' => $limit,
          'from' => $limit * ( $page - 1 ),
          'sort' => [
            $sort_field => [                                   
              'order' => strtolower($sort_direction)
            ]
          ]
        ]
      ];
      $results = $es_client->search($query);
      return $results;
    }

    public function searchTermUsingElasticsearch($term) {
        $es_client = app('ESClient');

        $query = [
            'index' => settings('app.elasticsearch_index_name'),
            'type' => 'product',
            'body' => [
                'query' => [
                  'bool' => [
                    'should' => [
                      [
                        'term' => [
                          'country' => get_current_country(),
                        ]
                      ],
                      [
                        'term' => [
                          'language' => get_current_language(),
                        ]
                      ],
                      [
                        'term' => [
                          '_all'  => $term
                        ]
                      ]
                    ],
                    "minimum_should_match" => 3
                  ]            
                ],
                'size' => 10
            ]
        ];

        $results = $es_client->search($query);

        $ids = [];

        if ($results['hits']['total'] > 0){
            foreach($results['hits']['hits'] as $hit){
                array_push($ids,$hit['_id']);
            }
        }

        $formatted_results = [];

        if (count($ids) > 0) {
            $formatted_results = \DB::select('
              SELECT title as rec_name, \'product\' as rec_type, url_key as url, thumbnail as thumb
              FROM products
              WHERE is_visible = TRUE
              AND country="'.get_current_country().'"
              AND language="'.get_current_language().'"
              AND id IN ("'.implode('","', $ids).'")
              AND is_parent is NOT true
              LIMIT 0, 10');
        }

        return $formatted_results;
    }

    //this method is for testing purposes only
    public function randomizeProductNames(){

        $full_seed = 'In vulputate velit esse molestie consequat vel illum dolore; eu feugiat nulla facilisis at? Iriure dolor in hendrerit vero eros et accumsan et iusto odio dignissim qui blandit praesent. Est usus legentis in iis qui facit eorum claritatem Investigationes, demonstraverunt lectores legere me lius quod. Ex ea commodo consequat duis autem vel eum? Et quinta decima eodem modo typi qui nunc nobis videntur parum clari fiant sollemnes in. Euismod tincidunt ut laoreet dolore magna aliquam erat volutpat ut wisi enim ad minim. Nobis eleifend option congue nihil imperdiet doming id. Praesent luptatum zzril delenit augue duis dolore te feugait nulla. At vero eros et accumsan et iusto odio dignissim qui blandit praesent. Qui sequitur mutationem consuetudium lectorum mirum est notare quam littera gothica quam nunc.';

        $full_seed = str_replace('.', '', $full_seed);
        $full_seed = str_replace(',', '', $full_seed);
        $full_seed = str_replace(';', '', $full_seed);
        $full_seed = str_replace('?', '', $full_seed);

        $words = explode(' ', $full_seed);

        $length = count($words);

        $products = Product::where('country', get_current_country() )->where('language', get_current_language() )->get();

        foreach($products as $prod){

            $name_words = rand(2,7);

            $name = '';

            for ($i =0; $i < $name_words; $i++ ){
                $name .= $words[rand(0, $length - 1)].($i != $name_words - 1 ? ' ' : '');
            }

            $prod->title = ucfirst($name);
            $prod->save();

        }

        die ();

    }

    public function handleSocialAuth($provider, $name, $email, $gender, $photo){

        $user = User::firstOrNew(array(
            'email' => $email
        ));

        if (!$user->role){
            $user->role = 'ROLE_FRONT_USER';
        }

        if (!$user->password){
            $user->password = \Hash::make('the password');
        }

        //to find if the db user needs update

        $socialData = [
            'avatar' => $photo,
            'email' => $email,
            'name' => $name,
            'gender' => $gender
        ];

        $dbData = [
            'avatar' => $user->profile_photo_url,
            'email' => $user->email,
            'name' => $user->name,
            'gender' => $user->gender
        ];

        if (!empty(array_diff($socialData, $dbData))) {
            $user->profile_photo_url = $photo;
            $user->email = $email;
            $user->name = $name;
            $user->gender = $gender;

            $user->save();
        }

        \Auth::login($user);

        return $user;

    }

    public function registerTermSearch($term, $results_count){

        $existent = null;

        if (\Auth::user()){

            $existent = \DB::table('term_searches')
                ->where('term_searches.used_term', '=', $term)
                ->where('term_searches.user_id', '=', \Auth::user()->id)
                ->first();

        } else {

            $existent = \DB::table('term_searches')
                ->where('term_searches.used_term', '=', $term)
                ->where('term_searches.user_id', null)
                ->first();

        }

        if ($existent == null){
            $search = new TermSearch();

            $search->used_term = $term;
            $search->results_found = $results_count;

            if (\Auth::user()){
                $search->user_id = \Auth::user()->id;
            }

            $search->save();

            return $search;

        } else {
            return $existent;
        }
    }

    public function walkTree($parent, $left = 1) {

        $right = $left + 1;

        $children = null;

        if (!is_object($parent)){
            $children = $parent['children'];
        } else {
            $children = \DB::table('categories')->where('country', get_current_country() )->where('language', get_current_language() )->where('is_visible', true)->where('parent_id', '=', $parent->id)->get();
        }

        foreach ($children as $child ){

            $right = $this->walkTree($child, $right);
            
        }

        if (is_object($parent)){
            if (!$parent->rgt && !$parent->lft){
                \DB::update('update categories SET lft = ?, rgt = ? WHERE id = ?', [$left, $right, $parent->id]);
            }
        }

        return $right + 1;
    }

    public function updateCategoryTree($language = null , $country = null){
        $language = ( $language == null ? get_current_language() : $language);
        $country = ($country == null ? get_current_country() : $country);

        $root_nodes = \DB::table('categories')->where('is_visible', true)->whereNull('parent_id')->where('country', $country )->where('language',$language )->get();

        $tree = array(
            'children' => $root_nodes
        );

        $this->walkTree($tree);
    }

    public function updateCategoriesTreeCache($language = null , $country = null){

        $language = ( $language == null ? get_current_language() : $language);
        $country = ($country == null ? get_current_country() : $country);

        $tree = Category::updateFullCategoryHierarchy();

        if (\Cache::has('categories_tree'.'_'.$country.'_'.$language)){
            \Cache::forget('categories_tree'.'_'.$country.'_'.$language);
        }

        \Cache::forever('categories_tree'.'_'.$country.'_'.$language, $tree);

        return $tree;

    }

    public function updateMenuConfigurationArray(){

        $menu_items = MenuConfiguration::with('category', 'category.visibleChildren')->orderBy('order', 'ASC')->get();

        $menu_definition = array();

        foreach($menu_items as $menu_item){

            $category = $menu_item->category;

            if ($category->is_visible){
                $element = array(
                    'title' => $category->title,
                    'order' => $menu_item->order,
                    'url' => prefixed_route($category->url_key),
                    'id' => $menu_item->id,
                    'display_children' => $menu_item->display_children
                );

                if ($menu_item->display_children && $category->children->count() > 0){

                    $children = array();

                    foreach($category->children as $child){
                        if ($child->is_visible){
                            $children[] = array(
                                'title' => $child->title,
                                'url' => prefixed_route($child->url_key),
                                'id' => $child->id
                            );
                        }
                    }

                    $element['children'] = $children;
                }

            }
            if (isset($element)){
                $menu_definition[] = $element;
            }
        }

        return $menu_definition;
    }

    public function getMenuConfigurationArray(){

        $country = get_current_country();
        $language = get_current_language(); 

        if (\Cache::has('main_navigation_definition'.'_'.$country.'_'.$language)){

            return \Cache::get('main_navigation_definition'.'_'.$country.'_'.$language);

        } else {

            $menu_definition = $this->updateMenuConfigurationArray();

            \Cache::forever('main_navigation_definition'.'_'.$country.'_'.$language, $menu_definition);

            return $menu_definition;
        }
    }

    public function updateMenuConfigurationCache(){

        $country = get_current_country();
        $language = get_current_language(); 

        if (\Cache::has('main_navigation_definition'.'_'.$country.'_'.$language)){
            \Cache::forget('main_navigation_definition'.'_'.$country.'_'.$language);
        }

        $menu_definition = $this->updateMenuConfigurationArray();

        \Cache::forever('main_navigation_definition'.'_'.$country.'_'.$language, $menu_definition);

    }

    public function updateColorDescendents($color_name, $color_value){

        $colors = \DB::table('product_properties')
            ->select('product_properties.value', 'color_codes.id')
            ->leftJoin('color_codes', 'product_properties.value', '=', 'color_codes.color_name')
            ->where('product_properties.name', 'Color')
            ->whereRaw(\DB::raw('LOWER(product_properties.value) LIKE "%'.strtolower($color_name).'%"'))
            ->groupBy('product_properties.value')
            ->get();

        foreach($colors as $color){

            if ($color->value != $color_name){

                if ($color->id){
                    $color_code = ColorCodes::find($color->id);
                } else {
                    $color_code = new ColorCodes();
                    $sessionCountry = isset($_COOKIE['sessionCountry']) ? $_COOKIE['sessionCountry'] : '';
                    $sessionLanguage = isset($_COOKIE['sessionLanguage']) ? $_COOKIE['sessionLanguage'] : '';
                    $currentLocale = get_current_locate($sessionCountry,$sessionLanguage);
                    $currentLocale = str_replace('/','',$currentLocale);
                    $pieces = explode("-", $currentLocale);
                    $color_code->country = strtolower($pieces[0]);
                    $color_code->language = strtolower($pieces[1]);
                }

                if ($color_code->color_code){
                    $defined_color = explode('/',$color_code->color_code);
                } else {
                    $defined_color = [];
                }

                //split the color name using "/" as the separator
                $color_parts = explode('/', $color->value);

                foreach($color_parts as $key=>$color_part){
                    if ($color_part){
                        if (strtolower(trim($color_part)) == strtolower($color_name)){
                            $defined_color[$key] = $color_value;
                        } else {
                            if (!isset($defined_color[$key])){
                                $defined_color[$key] = 'transparent';
                            }
                        }
                    }

                }
                $color_code->color_name = $color->value;
                $color_code->color_code = implode('/', $defined_color);
                if ($color_code !== 'transparent') {
                    $color_code->save();
                }
            }
        }

        return count($colors);

    }


    public function queryApiForTranslation($source_lang, $target_lang, $text_to_translate){

        $url = 'https://translate.yandex.net/api/v1.5/tr.json/translate?lang='.$source_lang.'-'.$target_lang.'&key=trnsl.1.1.20160818T182224Z.0de5909d916e057f.49cd16c40c3ca166c6491f8a0ee20c1ecfa0aed1';

        $post_vars = [
            'text' => $text_to_translate,
            'format' => 'html'
        ];

        $post_vars = http_build_query($post_vars);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vars);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_PROXY, 'http://proxy.sld.cu:3128');

        $body = curl_exec($ch);

        curl_close($ch);

        $result = json_decode($body, true);

        if ($result['code'] == '200'){
            //the translation worked fine

            return [
                'status' => 'success',
                'translated_text' => $result['text'][0]
            ];

        } else {
            //the translation failed

            return [
                'status' => 'fail',
                'message' => $result['message']
            ];

        }

    }
}