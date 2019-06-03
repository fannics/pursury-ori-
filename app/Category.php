<?php namespace ProjectCarrasco;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use ProjectCarrasco\Paginator\AppPaginator;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ProjectCarrasco\Category
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\ProjectCarrasco\Category[] $children
 * @method static \Illuminate\Database\Query\Builder|\ProjectCarrasco\Category visible()
 * @method static \Illuminate\Database\Query\Builder|\ProjectCarrasco\Category root()
 */
class Category extends Model {

    use SoftDeletes;

    protected $table = 'categories';
    protected $dates = ['deleted_at'];
    protected $fillable = ['id', 'title','categories','parent_id','url_key'
                            ,'short_description','is_visible','default_sorting','filters'
                            ,'meta_title','meta_description','meta_no_index','reference','lft','rgt','position'
                            ,'country','language','reference','img','img_thumbnail','img_alt','description'
    ];

    public function setParentIdAttribute($value)
    {
        $this->attributes['parent_id'] = $value ? $value: null;
    }

    public function setUrlKeyAttribute($value)
    {
        $this->attributes['url_key'] = addslashes($value);
    }

    public function setIsVisibleAttribute($value)
    {
        $this->attributes['is_visible'] = ($value == '1' ? true : false);
    }

    public function setMetaNoIndexAttribute($value)
    {

       $this->attributes['meta_no_index'] = ($value == '1' ? true : false);
    }
    public function children(){
        return $this->hasMany('ProjectCarrasco\Category', 'parent_id', 'id')->orderBy('lft', 'ASC');
    }

    public function visibleChildren(){
        return $this->hasMany('ProjectCarrasco\Category', 'parent_id', 'id')->where('is_visible', true)->orderBy('lft', 'ASC');
    }

    public function parent(){
        return $this->hasOne('ProjectCarrasco\Category', 'id', 'parent_id');
    }

    public function siblings(){

        $siblings = $this->query()->where('parent_id', '=', $this->getAttribute('parent_id'))->where('is_visible', true)->orderBy('lft', 'ASC')->get();

        return $siblings;
    }

    public function categoryTree(){

        $tree = \DB::table('categories')
            ->select()
            ->where('lft', '<', $this->lft)
            ->where('rgt', '>', $this->rgt)
            ->where('is_visible', true)
            ->where('country', get_current_country() )
            ->where('language', get_current_language() )
            ->orderBy('categories.lft')
            ->get(['categories.title', 'categories.url_key']);

        foreach($tree as $tree_leaf){
            $tree_nodes[] = array(
                'title' => $tree_leaf->title,
                'link' => $tree_leaf->url_key
            );
        }

        $tree_nodes[] = [
                'title' => $this->title,
                'link' => $this->url_key
            ];


        return $tree_nodes;
    }

    private static function buildTree( $elements, $parentId = 0) {

        if ($elements instanceof Collection)
        {
            $elements = $elements->toArray();
        }

        $branch = array();

        foreach ($elements as $element) {

            if ($element->parent_id == $parentId) {
                $children = self::buildTree($elements, $element->id);
                if ($children) {
                    $element->children = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    public static function updateFullCategoryHierarchy(){

        $categories = \DB::table('categories')
            ->select(\DB::raw('id, title, url_key, parent_id'))
            ->where('is_visible', true)
            ->where('country', get_current_country() )
            ->where('language', get_current_language() )
            ->orderByRaw('ISNULL(position), position ASC')
            ->get();

        $tree = self::buildTree($categories);

        return $tree;
    }

    public function scopeVisible($query){
        return $query->where('is_visible', '=', '1');
    }

    public function scopeRoot($query){
        return $query->whereNull('parent_id');
    }

    public static function findByUrlForRouting($url){
        return self::query()->visible()->where('url_key', '=', $url)->firstOrFail();
    }

    public static function sortableFields(){
        return array(
            'title' => 'Nombre',
            'popularity' => 'Popularidad',
            'price' => 'Precio'
        );
    }

    public function getApplicableFilters($subcategories){

        //check if the filters have applicable values

        //subcategories filters
//
        $filters = array();
//
        foreach($subcategories as $subcategory){

            if ($subcategory->filters){
                $keys = explode(';', $subcategory->filters);

                if ($keys){
                    $filters = array_merge($filters, $keys);
                }
            }

        }
//
        if ($filters){

            $filters = array_unique($filters);

            $filters = array_map(function($value){
                return trim($value);
            }, $filters);

            //join the filters in a MySQL array fashion
            $query_filters = '("'.implode('","', $filters).'")';

            $applicable_query = \DB::select(\DB::raw('SELECT COUNT(*) as amount, pp.value, pp.name FROM product_properties pp 
                                                        JOIN products p ON p.id = pp.product_id
                                                        JOIN product_category pc ON p.id = pc.product_id
                                                        JOIN categories c ON c.id = pc.category_id
                                                        WHERE c.lft BETWEEN '.$this->lft.' AND '.$this->rgt.'
                                                        AND pp.name IN '.$query_filters.'
                                                        GROUP BY pp.value HAVING amount > 0 ORDER BY pp.value ASC'));

            $filters = array_flip($filters);

            foreach($applicable_query as $applicable){
                if (!is_array($filters[$applicable->name])){
                    $filters[$applicable->name] = [];
                }

                $filters[$applicable->name][] = [
                    'match_amount' => $applicable->amount,
                    'property_value' => $applicable->value
                ];
            }

            $result = [];

            foreach($filters as $key=>$filter){
                if (is_array($filter) && count($filter) > 0){
                    $result[] = [
                        'filter_name' => $key,
                        'filter_values' => $filter
                    ];
                }
            }

            return $result;

        } else {
            return null;
        }
//
//            $filters = array_unique($filters);
//
//            //convert the filters into an array
//            // trim each one of the filters
//            $filters = array_map(function($value){
//                return trim($value);
//            }, $filters);
//
//            //join the filters in a MySQL array fashion
//            $query_filters = '("'.implode('","', $filters).'")';
//            $query_ids = '('.implode(',', $ids).')';
//
//            //perform the query finding from the category filters the ones that have values to filter for
////            $applicable_query = \DB::select(\DB::raw('SELECT COUNT(*) as amount, pp.name FROM product_properties pp
////                                    JOIN products p ON p.id = pp.product_id
////                                    JOIN product_category  pc ON pc.product_id = p.id
////                                    JOIN categories c ON pc.category_id = c.id
////                                    WHERE c.id IN '.$query_ids.' AND pp.name IN '.$query_filters.'
////                                    GROUP BY pp.name'));
//
//            //optimized
//            $applicable_query = \DB::select(\DB::raw('SELECT COUNT(*) as amount, pp.name FROM product_properties pp
//                                    JOIN products p ON p.id = pp.product_id
//                                    JOIN product_category  pc ON pc.product_id = p.id
//                                    JOIN categories c ON pc.category_id = c.id
//                                    WHERE pp.name IN '.$query_filters.'
//                                    AND c.lft BETWEEN '.$this->lft.'  AND '.$this->rgt.'
//                                    GROUP BY pp.name'));
//
//            $applicable_filters = array();
//
//            //if the filters returned by the query have the amount property greater than 0
//            // then add them to the applicable filters array
//            foreach($applicable_query as $filter){
//                if ($filter->amount > 0){
//                    $applicable_filters[] = $filter->name;
//                }
//            }

            return $applicable_filters;

//        } else {
//            return null;
//        }
    }

    public function parentsUntilRoot(){
        $categories = array();

        $current_category = $this;

        do{
            $categories[] = array(
                'id' => $current_category->id,
                'title' => $current_category->getAttribute('title'),
                'link' => $current_category->getAttribute('url_key')
            );

        } while($current_category = $current_category->parent()->getResults());

        return $categories;
    }

    public static function getApplicableValuesForFilter($category, $filter_name){

        $category_childrens = \DB::table('categories')
            ->select('categories.id')
            ->whereBetween('lft', [$category->lft, $category->rgt])
            ->where('country', get_current_country() )
            ->where('language', get_current_language() )
            ->get();

        $ids = array();

        foreach($category_childrens as $child_id){
            $ids[] = $child_id->id;
        }

        $ids = '('.implode(',', $ids).')';

        $filter_values = \DB::select(\DB::raw('SELECT DISTINCT(pp.value) as property_value, count(*) as match_amount FROM product_properties pp
                                                                            JOIN products p ON p.id = pp.product_id
                                                                            JOIN product_category pc ON p.id = pc.product_id
                                                                            JOIN categories c ON pc.category_id = c.id
                                                                            WHERE c.id IN '.$ids.' AND p.is_visible = TRUE AND pp.name = "'.$filter_name.'" GROUP BY pp.value ORDER BY property_value ASC'));

        return $filter_values;
    }

    public static function getApplicableValuesForProductFilter($category, $filter_name){
        switch($filter_name){
            case 'Marca':

                    $filter_values = \DB::select(\DB::raw('SELECT p.brand as property_value, count(*) as match_amount FROM products p
                                                                                    JOIN product_category pc ON p.id = pc.product_id
                                                                                    JOIN categories c ON pc.category_id = c.id
                                                                                    WHERE c.lft BETWEEN '.$category->lft.' AND '.$category->rgt.' AND p.is_visible = TRUE GROUP BY p.brand ORDER BY p.brand ASC'));
                    return $filter_values;
                break;
        };
    }

    public static function getFiltersByCategoryId($category_id){

        $category = self::find($category_id);

        $subcategories = \DB::table('categories')
            ->select()
            ->whereBetween('lft', [$category->lft, $category->rgt])
            ->where('is_visible', true)
            ->where('country', get_current_country() )
            ->where('language', get_current_language() )
           ->get();

        $filters = array();

        foreach($subcategories as $subcategory){
            $subcat_filters = $subcategory->filters;
            if ($subcat_filters){

                $subcat_filters = explode(';', $subcat_filters);

                $subcat_filters = array_map(function($v){
                    return trim($v);
                }, $subcat_filters);

                $filters = array_merge($filters, $subcat_filters);
            }
        }

        $filters = array_unique($filters);

        return $filters;
    }

    public static function getAllCategories($paginationFields){

        $query = \DB::table('categories as c')
            ->select(\DB::raw('c.id, c.title, c.url_key, c.is_visible, c.filters, p.title as parent_title'))
            ->leftJoin('categories as p', 'p.id', '=', 'c.parent_id')
            ->where('c.country', get_current_country() )
            ->where('c.language', get_current_language() )
            ->skip($paginationFields['offset'])
            ->take($paginationFields['itemsPerPage']);

        foreach($paginationFields['sorting'] as $sort_field){

            switch($sort_field['field']){
                case 'title':
                    $query->orderBy('c.title', $sort_field['dir']);
                    break;
                case 'parent_title':
                    $query->orderBy('parent_title', $sort_field['dir']);
                    break;
                case 'filters':
                    $query->orderBy('c.filters', $sort_field['dir']);
                    break;
                case 'is_active':
                    $query->orderBy('is_active', $sort_field['dir']);
                    break;
                case 'url_key':
                    $query->orderBy('url_key', $sort_field['dir']);
                    break;
            }

        }

        if (isset($paginationFields['filters']) && count($paginationFields['filters']) > 0){
            $query->where(function($query) use ($paginationFields){
                $query->orWhere('c.title', 'like', '%'.$paginationFields['filters'].'%')
                    ->orWhere('c.url_key', 'like', '%'.$paginationFields['filters'].'%')
                    ->orWhere('p.title', 'like', '%'.$paginationFields['filters'].'%')
                    ->orWhere('c.filters', 'like', '%'.$paginationFields['filters'].'%');
            });
        }
        
        $res = $query->get();
        $results = array();
        foreach($res as $item) {
          $item->url_key = prefixed_route($item->url_key);
          $results[] = $item;
        }

        return $results;
    }

    public static function getCategoriesCount($paginationFields = null){

        $query = \DB::table('categories as c')
            ->leftJoin('categories as p', 'p.id', '=', 'c.parent_id')
            ->where('c.country', get_current_country() )
            ->where('c.language', get_current_language() );


        if ($paginationFields && isset($paginationFields['filters']) && count($paginationFields['filters']) > 0){
            $query->where(function($query) use ($paginationFields){
                $query->orWhere('c.title', 'like', '%'.$paginationFields['filters'].'%')
                    ->orWhere('c.url_key', 'like', '%'.$paginationFields['filters'].'%')
                    ->orWhere('p.title', 'like', '%'.$paginationFields['filters'].'%')
                    ->orWhere('c.filters', 'like', '%'.$paginationFields['filters'].'%');
            });
        }

        return $query->count();
    }

    public static function paginateForAdmin($paginationFields){
        return new AppPaginator(
            self::getAllCategories($paginationFields),
            self::getCategoriesCount($paginationFields),
            $paginationFields['itemsPerPage'],
            ceil($paginationFields['offset'] / $paginationFields['itemsPerPage']) + 1
        );
    }

    public static function forExport(){

        return \DB::select("SELECT c.id as Category_ID, c.categories as Categories, c.short_description as Category_Short_Description,
                            c.title as Category_Title, c.url_key as Category_url_key, c.is_visible as Category_Is_Visible,
                            c.meta_title as Category_Meta_Title, c.meta_description as Category_Meta_Description,
                            c.meta_no_index as Category_Meta_Noindex, 'no' as Category_Delete,
                            c.filters as Category_Filters, c.default_sorting as Category_Default_Sorting, c.parent_id as Category_Parent, c.reference as Category_Reference
                            , c.img as Category_Img , c.img_thumbnail as Category_Img_Thumbnail , c.img_alt as Category_Img_Alt , c.description as Category_Description,
                            c.language as Category_Language , c.country as Category_Country
                            FROM categories c WHERE c.country='".get_current_country()."' and c.language='".get_current_language()."' ORDER BY c.parent_id ASC");

    }

    public static function getCategoryTreeForNavigation($category){

        $subtree = \DB::table('categories')
            ->where('is_visible', true)
            //subtree
            ->where(function($query) use ($category){
                //parent
                $query->where('id', $category->parent_id)
                    //siblings
                    ->orWhere('parent_id', $category->parent_id)
                    //children
                    ->orWhere('parent_id', $category->id);
            })
            ->where('country', get_current_country() )
            ->where('language', get_current_language() )
            ->orderByRaw(\DB::raw('ISNULL(position), position ASC'))
            ->get(['id', 'parent_id', 'url_key', 'title']);

        $tree = self::buildTree($subtree, $category->parent_id);

        $parent = null;
        $has_children = false;

        foreach($subtree as $subtree_elem){
            if ($subtree_elem->id == $category->parent_id){
                //locate the parent in the subtree
                $parent = $subtree_elem;
            }
            //check if the category has children in order to add parent siblings on missing children
            if ($subtree_elem->parent_id == $category->id){
                $has_children = true;
            }
        }

        if ($parent){
            $parent->children = $tree;

            $tree = [$parent];

            if (!$has_children){
                $parent_siblings = \DB::table('categories')
                    ->where('is_visible', true)
                    ->where('parent_id', $parent->parent_id)
                    ->where('country', get_current_country() )
                    ->where('language', get_current_language() )
                    ->orderByRaw(\DB::raw('ISNULL(position), position ASC'))
                    ->get(['id', 'parent_id', 'url_key', 'title'])->toArray();

//                sort the parent siblings according to the sort field and direction

                $tree = array_map(function($value) use ($tree){
                    if ($value->id == $tree[0]->id){
                        return $tree[0];
                    } else {
                        return $value;
                    }
                }, $parent_siblings);

            }
        }
        return $tree;
    }

    public function toggle(){
        $this->is_visible = $this->is_visible == true ? false : true;
    }
}
