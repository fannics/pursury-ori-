<?php namespace ProjectCarrasco;

use Illuminate\Database\Eloquent\Model;
use ProjectCarrasco\Paginator\AppPaginator;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model {

    use SoftDeletes;

	protected $table = 'stores';
    protected $dates = ['deleted_at'];
    protected $fillable = ['id', 'name','url_key','is_visible','meta_title','meta_description',
                         'meta_noindex','logo','logo_thumb','country', 'language'];
    public static $requiredFields = [
        'Store_ID',
        'Store_Name',
        'Store_url_key',
        'Store_Is_Visible',
        'Store_Meta_Title',
        'Store_Meta_Description',
        'Store_Meta_Noindex',
        'Store_Delete',
        'Store_Logo',
        'Store_Logo_Thumb',
        'Store_Language',
        'Store_Country'
    ];

    public function products()
    {
        return $this->hasMany(Product::class,'store');
    }

    public static function paginateForAdmin($paginationFields){
        return new AppPaginator(
            self::getAllStores($paginationFields),
            self::getStoresCount($paginationFields),
            $paginationFields['itemsPerPage'],
            ceil($paginationFields['offset'] / $paginationFields['itemsPerPage']) + 1
        );
    }

    public static function getAllStores($paginationFields){

        $query = self::where('country', get_current_country() )
            ->where('language', get_current_language() )
            ->skip($paginationFields['offset'])
            ->take($paginationFields['itemsPerPage']);

        foreach($paginationFields['sorting'] as $sort_field){

            switch($sort_field['field']){
                case 'name':
                    $query->orderBy('name', $sort_field['dir']);
                    break;

                case 'is_visible':
                    $query->orderBy('is_visible', $sort_field['dir']);
                    break;
                case 'url_key':
                    $query->orderBy('url_key', $sort_field['dir']);
                    break;
            }

        }

        if (isset($paginationFields['filters']) && count($paginationFields['filters']) > 0){
            $query->where(function($query) use ($paginationFields){
                $query->orWhere('name', 'like', '%'.$paginationFields['filters'].'%')
                    ->orWhere('url_key', 'like', '%'.$paginationFields['filters'].'%');
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

    public static function getStoresCount($paginationFields = null){

        $query = self::where('country', get_current_country() )
            ->where('language', get_current_language() );


        if ($paginationFields && isset($paginationFields['filters']) && count($paginationFields['filters']) > 0){
            $query->where(function($query) use ($paginationFields){
                $query->orWhere('name', 'like', '%'.$paginationFields['filters'].'%')
                    ->orWhere('url_key', 'like', '%'.$paginationFields['filters'].'%');
            });
        }

        return $query->count();
    }

    public static function forExport(){

        return \DB::select("SELECT s.id as Store_ID, s.name as Store_Name,
                             s.url_key as Store_url_key, s.is_visible as Store_Is_Visible,
                            s.meta_title as Store_Meta_Title, s.meta_description as Store_Meta_Description,
                            s.meta_noindex as Store_Meta_Noindex, 'no' as Store_Delete,
                             s.logo as Store_Logo , s.logo_thumb as Store_Logo_Thumb ,
                            s.language as Store_Language , s.country as Store_Country
                            FROM stores s WHERE s.country='".get_current_country()."' and s.language='".get_current_language()."' ORDER BY s.id ASC");

    }

}
