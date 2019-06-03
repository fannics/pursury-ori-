<?php namespace ProjectCarrasco;

use Illuminate\Database\Eloquent\Model;
use ProjectCarrasco\Paginator\AppPaginator;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model {

    use SoftDeletes;

	protected $table = 'brands';
    protected $dates = ['deleted_at'];
    protected $fillable = ['id','name','short_description','title','url_key','is_visible','meta_title'
                        ,'meta_description','meta_noindex','default_sorting','image','country', 'language','description'];
    public static $requiredFields = [
        'Brand_ID',
        'Brand_Title',
        'Brand_Short_Description',
        'Brand_Name',
        'Brand_url_key',
        'Brand_Is_Visible',
        'Brand_Meta_Title',
        'Brand_Meta_Description',
        'Brand_Meta_Noindex',
        'Brand_Delete',
        'Brand_Default_Sorting',
        'Brand_Image',
        'Brand_Description',
        'Brand_Language',
        'Brand_Country'
    ];

    public function products()
    {
        return $this->hasMany(Product::class,'brand');
    }

    public static function paginateForAdmin($paginationFields){
        return new AppPaginator(
            self::getAllBrands($paginationFields),
            self::getBrandsCount($paginationFields),
            $paginationFields['itemsPerPage'],
            ceil($paginationFields['offset'] / $paginationFields['itemsPerPage']) + 1
        );
    }

    public static function getAllBrands($paginationFields){

        $query = self::where('country', get_current_country() )
            ->where('language', get_current_language() )
            ->skip($paginationFields['offset'])
            ->take($paginationFields['itemsPerPage']);

        foreach($paginationFields['sorting'] as $sort_field){

            switch($sort_field['field']){
                case 'title':
                    $query->orderBy('title', $sort_field['dir']);
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
                $query->orWhere('title', 'like', '%'.$paginationFields['filters'].'%')
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

    public static function getBrandsCount($paginationFields = null){

        $query = self::where('country', get_current_country() )
            ->where('language', get_current_language() );


        if ($paginationFields && isset($paginationFields['filters']) && count($paginationFields['filters']) > 0){
            $query->where(function($query) use ($paginationFields){
                $query->orWhere('title', 'like', '%'.$paginationFields['filters'].'%')
                    ->orWhere('url_key', 'like', '%'.$paginationFields['filters'].'%');
            });
        }

        return $query->count();
    }

    public static function forExport(){

        return \DB::select("SELECT b.id as Brand_ID, b.title as Brand_Title, b.short_description as Brand_Short_Description,
                            b.name as Brand_Name, b.url_key as Brand_url_key, b.is_visible as Brand_Is_Visible,
                            b.meta_title as Brand_Meta_Title, b.meta_description as Brand_Meta_Description,
                            b.meta_noindex as Brand_Meta_Noindex, 'no' as Brand_Delete,
                            b.default_sorting as Brand_Default_Sorting,
                             b.image as Brand_Image , b.description as Brand_Description,
                            b.language as Brand_Language , b.country as Brand_Country
                            FROM brands b WHERE b.country='".get_current_country()."' and b.language='".get_current_language()."' ORDER BY b.id ASC");

    }

}
