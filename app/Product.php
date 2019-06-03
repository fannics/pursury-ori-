<?php namespace ProjectCarrasco;

use Illuminate\Database\Eloquent\Model;
use ProjectCarrasco\Paginator\AppPaginator;

/**
 * ProjectCarrasco\Product
 *
 * @property-read \ProjectCarrasco\Category $category
 */
class Product extends Model {

    protected $table = 'products';
    protected $fillable = ['product_id','title','short_description','description','url_key','is_visible','image',
                        'thumbnail','price','destination_url','hits','shop_visits','popularity','brand','previous_price',
                        'meta_title','meta_description','meta_index','category_sort','country','language','parent_id','store',
                        'image_alt','image_alt','winner','stock','parent_filters','is_parent'];
    public function categories(){
        return $this->belongsToMany('ProjectCarrasco\Category', 'product_category');
    }

    public function parentCategories(){
        $categories = array();
        if ($this->categories()){

            foreach($this->categories as $cat){
                $categories[] = $cat->title;
            }
        }

        return implode(', ', $categories);
    }

    public function hasCategory($category_id){

        foreach($this->categories as $cat){
            if ($cat->id == $category_id){
                return true;
            }
        }

        return false;
    }

    public function discount(){

        $discount = 100 - ( floatval($this->getAttribute('price')) * 100 / floatval($this->getAttribute('previous_price')));

        return round($discount);
    }

    public static function filterableFields(){
        return array(
            'price' => array('type' => 'slider', 'name' => 'Precio'),
            'brand' => array('type' => 'string', 'name' => 'Marca')
        );
    }

    public function onUserWishlist($user_id){

        if (!$user_id)
            return null;

        $on_wishlist = \DB::table('wishlists')
            ->select()
            ->where('product_id', $this->getAttribute('id'))
            ->where('user_id', $user_id)
            ->first();

        return $on_wishlist;
    }

    public static function getProductsOnUserWishlist($user_id){
        return \DB::table('products')
            ->select()
            ->where('country', get_current_country() )
            ->where('language', get_current_language() )
            ->join('wishlists', 'wishlists.product_id', '=', 'products.id')
            ->where('products.is_visible', 1)
            ->where('wishlists.user_id', $user_id)
            ->orderBy('wishlists.created_at', 'DESC')
            ->groupBy('wishlists.id')
            ->get();
    }

    public function categoryTree(){

        $category_tree = array();

        $categories = $this->categories;

        if ($categories->count() > 0){

            $subtree = array();

            foreach($categories as $parent_category){

                $current_category = $parent_category;

                do{
                    $subtree[] = array(
                        'id' => $current_category->id,
                        'title' => $current_category->getAttribute('title'),
                        'link' => $current_category->getAttribute('url_key')
                    );

                } while($current_category = $current_category->parent()->getResults());

                $category_tree[] = array_reverse($subtree);
                $subtree = array();
            }
        }

        return $category_tree;
    }

    public static function findByProductId($product_id){
        return self::where('product_id', '=', $product_id)
          ->where('country', get_current_country())
          ->where('language', get_current_language())
          ->firstOrFail();
    }

    public function scopeVisible($query){
        return $query->where('is_visible', 1);
    }

    public static function findByUrlForRouting($url){
        return self::query()->visible()->where('url_key', '=', $url)->firstOrFail();
    }

    public static function productsForHomeByRanking(){
        return self::query()->visible()->orderBy('popularity', 'DESC');
    }

    public static function extractFilters($category_id, $filters){

        $product_filters = array();
        $category_filters = array();

        $product_filters_map = self::filterableFields();
        $category_filters_map = Category::getFiltersByCategoryId($category_id);

        $product_filter_names = array();

        //extract the product field filters from the filters array
        foreach ($product_filters_map as $map_key=>$product_filter){
            foreach($filters as $key=>$filter){
                if (strcasecmp($product_filter['name'], $key) == 0){
                    $product_filters[$map_key] = $filter;
                }
            }
        }

        //extract category filters from filters array
        foreach($category_filters_map as $map_key=>$category_filter){
            foreach($filters as $key=>$filter){
                if (strcasecmp($category_filter, $key) == 0){
                    $category_filters[$key] = $filter;
                }
            }
        }

        return array(
            'product_filters' => $product_filters,
            'category_filters' => $category_filters
        );

    }

    public static function findTotalByTerm($term){

        $query = \DB::table('products')->where('country', get_current_country() )->where('language', get_current_language() )->where('products.is_parent','!=',true);

        $query = $query->select( \DB::raw('COUNT(*) as amount'));

        $query = $query->where('products.is_visible', 1)
            ->whereRaw('(title LIKE "%'.$term.'%" OR description LIKE "%'.$term.'%" )')
            ->leftJoin('wishlists','products.id', '=', 'wishlists.product_id');

        return $query->first()->amount;
    }

    public static function findByTerm($term, $page, $limit, $sorting_field, $sorting_direction){

        $query = \DB::table('products')->where('country', get_current_country() )->where('language', get_current_language() )->where('products.is_parent','!=',true);

        $query = $query->select( \DB::raw('products.id, products.title, products.price, products.brand, products.previous_price, products.url_key, products.thumbnail, products.previous_price, wishlists.id as on_wishlist, products.image_alt'));

        $query = $query->where('products.is_visible', 1)
            ->whereRaw('(products.title LIKE "%'.$term.'%" OR products.description LIKE "%'.$term.'%")')
            ->leftJoin('wishlists','products.id', '=', 'wishlists.product_id');

        $query = $query->groupBy('products.id')
            ->orderBy('title', 'DESC');

        $query = $query->take($limit)->offset($limit * ($page - 1))->orderBy($sorting_field, $sorting_direction);

        return $query->get();
    }


    public static function getByCategoryIdQuery($category_id, $parent_id, $filters, $exclude, $filter_policy = 'restrictive', $onlyChild=null) {
        if (!empty($onlyChild)) {
          $query = \DB::table('products')->where('products.country', get_current_country() )->where('products.language', get_current_language() )->whereNotNull('parent_id');
        }
        else {
          $query = \DB::table('products')->where('products.country', get_current_country() )->where('products.language', get_current_language() )->whereNull('parent_id');
        }
        $query = $query->join('brands as product_brands','product_brands.id','=','products.brand');
        if (is_numeric($category_id)){
            $category = Category::find($category_id);
            $child_categories = \DB::table('categories')->whereBetween('lft', array($category->lft, $category->rgt))->where('is_visible', '=', true)->where('country', $category->country )->where('language',  $category->language )->get();
            $ids = array();
            foreach($child_categories as $child_category){
                $ids[] = $child_category->id;
            }
        } 
        else {
            $ids = array();
            foreach($category_id as $category_element){
                $child_categories = \DB::table('categories')->whereBetween('lft', array($category_element->lft, $category_element->rgt))->where('is_visible', '=', true)->where('country', $category_element->country )->where('language', $category_element->language )->get();
                foreach($child_categories as $child_category){
                    $ids[] = $child_category->id;
                }
            }
        }
        $query = $query->join('product_category', 'products.id', '=', 'product_category.product_id')
            ->whereIn('product_category.category_id', $ids);
        $query = $query->where('products.is_visible', 1);
        if ($exclude){
            $query = $query->where('products.id', '<>', $exclude);
        }
        if (is_array($filters) && count($filters) > 0){
            if (isset($filters['tag'])) {
                $grouped_tags = [];
                if (is_array($filters['tag'])) {
                    foreach($filters['tag'] as $tag){
                        if ($tag){
                            $tag_obj = Tags::find($tag);
                            if ($tag_obj){
                                if (isset($grouped_tags[$tag_obj->tag_name])){
                                    $grouped_tags[$tag_obj->tag_name][] = 'pt.tag_id = '. $tag;
                                } else {
                                    $grouped_tags[$tag_obj->tag_name] = [];
                                    $grouped_tags[$tag_obj->tag_name][] ='pt.tag_id = '. $tag;
                                }
                            }
                        }
                    }
                } else {
                    $tag_obj = Tags::find($filters['tag']);
                    if ($tag_obj){
                        $grouped_tags[$tag_obj->tag_name] = [];
                        $grouped_tags[$tag_obj->tag_name][] ='pt.tag_id = '. $filters['tag'];
                    }
                }

                $subquery_segments = [];

                foreach($grouped_tags as $gt){
                    $subquery_segments[] = '('.implode(' OR ', $gt).')';
                }

                $subquery_conditions = implode(' OR ', $subquery_segments);

                if ($subquery_conditions){

                    $subquery = 'SELECT pt.product_id, pt.tag_id, COUNT(*) as amount FROM product_tags pt WHERE '.$subquery_conditions.' GROUP BY pt.product_id HAVING amount = '.count($subquery_segments);

                    if ($filter_policy == 'restrictive'){
                        $query->join( \DB::raw('('.$subquery.') virt '), function($join){
                            $join->on('products.id', '=', 'virt.product_id');
                        });
                    } else {
                        $query->leftJoin( \DB::raw('('.$subquery.') virt '), function($join){
                            $join->on('products.id', '=', 'virt.product_id');
                        });
                    }

                }
            }

            if (isset($filters['brand']) && $filters['brand']){
                if (is_array($filters['brand'])){
                    $query = $query->whereIn('product_brands.title', $filters['brand']);
                } else {
                    $query = $query->where('product_brands.title', $filters['brand']);
                }
            }

            if (isset($filters['price']) && $filters['price']){

                if (strpos($filters['price'], '-') !== false){

                    $values = explode('-', $filters['price']);

                    if (isset($values[0]) && is_numeric($values[0])){

                        if (isset($values[1]) && is_numeric($values[1])){
                            $query = $query->whereBetween('products.price', $values);
                        } else {
                            $query = $query->where('products.price', '>=', $values[0]);
                        }

                    }

                } else {
                    $query = $query->where('products.price', '>=', $filters['price']);
                }

            }

        }
        
        return $query;

    }

    public static function getByCategoryIdUsingTags($category_id, $parent_id, $filters, $page, $itemsPerPage, $exclude, $sorting_field, $sorting_direction) {
      $user_id = \Auth::user() ? \Auth::user()->id : null;
      $query = self::getByCategoryIdQuery($category_id, $parent_id, $filters, $exclude);
      $query = $query->select( \DB::raw('products.id, products.destination_url, products.title, products.price, product_brands.title as brand, products.previous_price, products.url_key, products.thumbnail, products.previous_price, wishlists.id as on_wishlist, NULL as discount, products.image_alt, products.is_parent, products.product_id, products.parent_id'));
      $query = $query->leftJoin('wishlists', 
        function($join) use ($user_id) {
          $join->on('wishlists.product_id', '=', 'products.id')
            ->where('wishlists.user_id', '=',$user_id);
        }
      );
      $query = $query
        ->orderByRaw('ISNULL(category_sort) ASC, category_sort ASC, '.$sorting_field.' '.$sorting_direction)
        ->groupBy('products.id');

      $query = $query->take($itemsPerPage)->skip(($page - 1) * $itemsPerPage);
      return $query->get();
    }

    public static function getByCategoryIdCountUsingTags($category_id, $parent_id, $filters){
        $query = self::getByCategoryIdQuery($category_id, $parent_id, $filters, null);
        $query = $query->select( \DB::raw('COUNT(products.id) as amount'));
        return $query->first()->amount;
    }

    public static function getProductListFromIdsList($ids, &$theProducts = null) {
      $query = \DB::table('products')->where('country', get_current_country() )->where('language', get_current_language() )->where('is_parent', '!=',true);
      $query = $query->select( \DB::raw('products.id, products.destination_url, products.title, products.price, products.brand, products.previous_price, products.url_key, products.thumbnail, products.previous_price, wishlists.id as on_wishlist, products.image_alt, products.is_parent, products.product_id, products.parent_id'))
        ->where('products.is_visible', 1)
        ->leftJoin('wishlists','products.id', '=', 'wishlists.product_id');
      $query = $query->whereIn('products.id', $ids);
      $products = $query->get();
      $results = array_flip($ids);

      foreach($products as $prod) {
        $results[$prod->id] = $prod;
      }
        
      $unsetResults = 0;
      
      foreach ($results as $key => $value) {
        
        if ( empty($results[$key]->url_key) ) {
          unset($results[$key]);
          $unsetResults = $unsetResults + 1;          
        }        
      }
      unset($products, $ids);
        
      if ( !empty($theProducts) ) {
        
        if (count($results) == 0) {
          $theProducts['hits']['total'] = 0; 
        }
        else {
          $theProducts['hits']['total'] = $theProducts['hits']['total'] - $unsetResults;
        }
      
      }
      
      return $results;
    }

    public static function paginateByCategoryUsingTags($category_id, $parent_id,$filters,$page, $itemsPerPage, $exclude = null, $sorting_field = 'popularity', $sorting_direction = 'DESC'){
        return new AppPaginator(
            self::getByCategoryIdUsingTags($category_id, $parent_id, $filters ,$page, $itemsPerPage, $exclude, $sorting_field, $sorting_direction),
            self::getByCategoryIdCountUsingTags($category_id, $parent_id, $filters),
            $itemsPerPage,
            $page
        );
    }

    public static function paginateByCategory($category_id, $parent_id,$filters,$page, $itemsPerPage, $exclude = null, $sorting_field = 'popularity', $sorting_direction = 'DESC'){

        $applicable_filters = self::extractFilters($category_id, $filters);

        return new AppPaginator(
            self::getByCategoryId($category_id, $parent_id, $applicable_filters ,$page, $itemsPerPage, $exclude, $sorting_field, $sorting_direction),
            self::getByCategoryIdCount($category_id, $parent_id, $applicable_filters),
            $itemsPerPage,
            $page
        );
    }

    public static function similarProducts($categories, $filters, $page, $itemsPerPage, $exclude = null, $sorting_field = 'popularity', $sorting_direction = 'DESC'){

        $query = self::getByCategoryIdQuery($categories, null, null, $exclude);

        dd($query);

        return new AppPaginator(
            self::getByCategoryId($categories, null, $applicable_filters ,$page, $itemsPerPage, $exclude, $sorting_field, $sorting_direction),
            self::getByCategoryIdCount($categories, null, $applicable_filters),
            $itemsPerPage,
            $page
        );                                             
    }

    public static function paginateForHome($page, $itemsPerPage, $sort_field = 'popularity', $sort_direction = 'DESC') {
        $products = \DB::table('products as p')
            ->where('country', get_current_country() )
            ->where('language', get_current_language() )
            ->select(\DB::raw('p.id, p.title, p.url_key, p.price, p.previous_price, p.destination_url, p.thumbnail, p.popularity, w.id as on_wishlist, p.image_alt, p.is_parent, p.product_id, p.parent_id'))
            ->leftJoin('wishlists as w', 'w.product_id', '=', 'p.id')
            ->where('p.is_visible', true)
            ->whereNull('p.parent_id')
            ->take($itemsPerPage, ($page - 1) * $itemsPerPage)
            ->skip(($page - 1) * $itemsPerPage)
            ->orderBy($sort_field, $sort_direction)
            ->get();    
        return new AppPaginator(
            $products,
            self::query()
              ->where('country', get_current_country() )
              ->where('language', get_current_language() )
              ->whereNull('parent_id')
              ->visible()->count(),
            $itemsPerPage,
            $page
        );
    }

    public static function getAllProducts($pagination_fields) {
      $res = self::with('categories')
        ->where('country', get_current_country() )
        ->where('language', get_current_language() )
        ->skip($pagination_fields['offset'])
        ->take($pagination_fields['itemsPerPage']);

      foreach($pagination_fields['sorting'] as $sort_field) {

        switch($sort_field['field']) {
          case 'title':
            $res->orderBy('title', $sort_field['dir']);
          break;
          case 'categories':
            $res->orderBy('title', $sort_field['dir']);
          break;
          case 'price':
            $res->orderBy('price', $sort_field['dir']);
          break;
          case 'is_active':
            $res->orderBy('is_active', $sort_field['dir']);
          break;
          case 'url_key':
            $res->orderBy('url_key', $sort_field['dir']);
          break;
        }
        
      }

      if (isset($pagination_fields['filters']) && $pagination_fields['filters']) {
        
        $res->where(
         
          function($query) use ($pagination_fields) {
            $query->where('products.title', 'LIKE', '%'.$pagination_fields['filters'].'%')
              ->orWhere('products.price', 'LIKE', '%'.$pagination_fields['filters'].'%')
              ->orWhere('url_key', 'LIKE', '%'.$pagination_fields['filters'].'%');
            }
            
        );
      }

      $res = $res->get(array('products.title', 'products.url_key', 'products.id', 'products.price', 'products.is_visible', 'products.id'));
      $results = array();

      foreach($res as $item) {
        $i = array(
          'id' => $item->id,
          'title' => $item->title,
          'price' => print_price($item->price),
          'is_visible' => $item->is_visible,
          'url_key' => prefixed_route($item->url_key)
        );
        $cats = array();

        if ($item->categories) {
        
          foreach($item->categories as $cat){
            $cats[] = $cat->title;
          }
          
        }

        $i['categories'] = implode(', ', $cats);
        $results[] = $i;
      }
        
      return $results;
    }

    public static function getProductsCount($pagination_fields){


        $res = self::query()->where('country', get_current_country() )->where('language', get_current_language() );

        if (isset($pagination_fields['filters']) && $pagination_fields['filters']){

            $res->where(function($query) use ($pagination_fields){
                $query->where('products.title', 'LIKE', '%'.$pagination_fields['filters'].'%')
                    ->orWhere('products.price', 'LIKE', '%'.$pagination_fields['filters'].'%')
                    ->orWhere('url_key', 'LIKE', '%'.$pagination_fields['filters'].'%');
            });

        }

        return $res->count();
    }

    public static function paginateForAdmin($paginationFields){
        return new AppPaginator(
            self::getAllProducts($paginationFields),
            self::getProductsCount($paginationFields),
            $paginationFields['itemsPerPage'],
            ceil($paginationFields['offset'] / $paginationFields['itemsPerPage']) + 1
        );
    }


    public static function mostSeen($page, $itemsPerPage) {
        return \DB::table('products as p')
            ->select(\DB::raw('p.id, p.product_id, p.title, p.brand, p.destination_url, p.url_key, p.is_visible, p.price, p.previous_price, p.thumbnail, p.hits, p.shop_visits, ( (p.shop_visits + p.hits) / 2 ) as popularity, p.image_alt, p.parent_id'))
            ->where('country', get_current_country() )
            ->where('language', get_current_language() )
            ->where(
              function($query) {
                $query->where('p.hits', '>', 0);
                $query->orWhere('p.shop_visits', '>', 0);
              }
            )
            ->orderBy('popularity', 'DESC')
            ->take($itemsPerPage, ($page - 1) * $itemsPerPage)
            ->skip(($page - 1) * $itemsPerPage)
            ->get();
    }

    public static function getMostSeenProducts($page, $itemsPerPage){
        return new AppPaginator(
            self::mostSeen($page, $itemsPerPage, null),
            self::getProductsCount(null),
            $itemsPerPage,
            $page
        );
    }

    public static function forExport($limit, $offset){

        return \DB::select(\DB::raw(
            'SELECT products.id, title as Product_Title, `products`.product_id as Product_ID, "" as Product_Category ,short_description as Product_Short_Description,
            description as Product_Description, url_key as Product_url_key, is_visible as Product_Is_Visible, meta_title as Product_Meta_Title,
            thumbnail as Product_Image_Thumbnail, image as Product_Image, meta_description as Product_Meta_Description, meta_index as Product_Meta_Noindex,
            "no" as Product_Delete, price as Product_Price, destination_url as Product_Ext_Link, brand as Product_Brand, previous_price as Product_Previous_Price,
            category_sort AS Product_Position, image_alt AS Product_Image_Alt  , category_sort as Product_Category_Sort 
             , products.country as Product_Country , products.language as Product_Language , parent_id as Product_Parent , store as Product_Store , shipping_cost as Product_Shipping_Cost
            , stock as Product_Stock , parent_filters as Product_Parent_Filter , is_parent as Product_Is_Parent , GROUP_CONCAT(product_category.category_id) as categories
           , p_t.props
             FROM products  JOIN product_category on `products`.id = product_category.product_id  JOIN (
             
             SELECT    
               products.id  as t_product_id,  GROUP_CONCAT(CONCAT(\'{"name":"\', t.tag_name, \'", "value"  :"\',t.tag_value,\'"}\') SEPARATOR "jsonSeparator") props 
               FROM
                   tags AS t
                    JOIN product_tags AS p_tags
                     ON  p_tags.tag_id = t.id  AND t.language = "'.get_current_language().'" AND t.country= "'.get_current_country().'"
                     RIGHT JOIN products on p_tags.product_id = products.id  where products.country = "'.get_current_country().'" AND products.language="'.get_current_language().'"
                     GROUP BY products.id) AS p_t on p_t.t_product_id = products.id    WHERE products.country="'.get_current_country().'" AND products.language="'.get_current_language().'"   GROUP BY `products`.Product_ID'  . ' LIMIT '.$limit.' OFFSET '.$offset
        ));
    }

    public static function countForExport(){
        return self::query()->count();
    }

    public static function getExtendedFields(){
        return \DB::select('SELECT DISTINCT(t.tag_name) FROM tags t  where t.language= "'.get_current_language(). '" AND t.country="'. get_current_country().'"');
    }

    public static function getPropertiesForProductId($product_id){
        return \DB::select('SELECT pp.name, pp.value FROM product_properties as pp JOIN products as p ON p.id = pp.product_id WHERE pp.country="'.get_current_country().'" and pp.language="'.get_current_language().'" and p.product_id = "'.$product_id.'"');
    }

    public static function getPriceRange(){
        $price_range = \DB::table('products')
            ->where('country', get_current_country() )
            ->where('language', get_current_language() )
            ->selectRaw('MIN(products.price) as min_price, MAX(products.price) as max_price')
            ->first();
        return $price_range;
    }

    public static function findDupes(){

        $dupes = \DB::table('products')
            ->where('country', get_current_country() )
            ->where('language', get_current_language() )
            ->select(\DB::raw('COUNT(*) as amount, REPLACE(url_key, \'/\', \'\') rep_url'))
            ->having('amount', '>', 1)
            ->groupBy('rep_url');

        return $dupes->get();
    }

    public static function findWithMissingFields(){

        $products = self::where('title', '')->orWhereNull('title')
            ->orWhere('description', '')->orWhereNull('description')
            ->orWhere('url_key', '')->orWhereNull('url_key')
            ->orWhere('price', '')->orWhereNull('price')->orWhere('price', 0)
            ->orWhere('image', '')->orWhereNull('image')
            ->orWhere('thumbnail', '')->orWhereNull('thumbnail')
            ->orWhere('brand', '')->orWhereNull('brand')
            ->orWhere('destination_url', '')->orWhereNull('destination_url')
            ->get();

        return $products;
    }

    public static function getProductBrands($filter_source, $filter_source_id, $filters){

        switch ($filter_source) {
            case 'category_page':

                $filters['brand'] = null;

                $query = self::getByCategoryIdQuery($filter_source_id, null, $filters, null);

                $query = $query->select(\DB::raw('brands.title as brand, COUNT(*) as amount'))
                    ->join('brands','products.brand','=','brands.id')
                    ->distinct()
                    ->groupBy('products.brand')
                    ->get();

                return $query;

                break;
            case 'search':
                break;
        }
    }

    public static function getMatchingPriceRange($filters, $filter_source, $filter_source_id){

        switch($filter_source){
            case 'category_page':

                $filters['price'] = null;

                $query = self::getByCategoryIdQuery($filter_source_id, null, $filters, null);

                $query = $query->select(\DB::raw('MIN(products.price) as min_price, MAX(products.price) as max_price'))
                    ->distinct()
                    ->first();

                return $query;

                break;
            case 'search':
                break;
        }

    }

    public function toggle(){
        $this->is_visible = $this->is_visible == true ? false : true;
    }

}
