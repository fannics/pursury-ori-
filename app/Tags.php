<?php namespace ProjectCarrasco;

use Illuminate\Database\Eloquent\Model;

class Tags extends Model {

    protected $table = 'tags';

    public $timestamps = false;


    public static function getUsedTagsFromArray($ids){

        if (!is_array($ids)){
            $ids = [$ids];
        }

        $used_tags = \DB::table('tags')->whereIn('id', $ids)->get(['id', 'tag_name']);

        $res = [];

        foreach($used_tags as $tag){
            $res[$tag->id] = $tag->tag_name;
        }

        return $res;

    }

    public static function getColorTags($filter_source, $filter_source_id, $filters, $onlyChild=null) {
      $used_tags = [];
      $tag_name = 'Color';

      if (isset($filters['tag']) && count($filters['tag']) > 0) {
        $used_tags = self::getUsedTagsFromArray($filters['tag']);
        if (count($used_tags) > 0) {
          if (is_array($filters['tag'])) {
            $filters['tag'] = array_filter($filters['tag'], 
                                function($v) use($used_tags, $tag_name) {
                                  if ($used_tags[$v] !== $tag_name) {
                                    return $v;
                                  }
                                }
                              );
          } 
          else {
            if ($used_tags[$filters['tag']] == $tag_name) { 
              $filters['tag'] = null;
            }
          }
        }
      }

      switch($filter_source) {
        case 'category_page':
          if (!empty($onlyChild)) {
            $query = Product::getByCategoryIdQuery(intval($filter_source_id), null, $filters, null, 'restrictive', true);
          }
          else {
            $query = Product::getByCategoryIdQuery(intval($filter_source_id), null, $filters, null);
          }
          
          $query = $query->join('product_tags', 'products.id', '=', 'product_tags.product_id')
            ->join('tags', 'product_tags.tag_id', '=', 'tags.id')
            ->leftJoin('color_codes', 'tags.tag_value', '=', 'color_codes.color_name')
            ->where('tags.tag_name', 'Color')
            ->orderBy('tags.tag_value', 'ASC')
            ->groupBy('tags.tag_value')
            ->select(\DB::raw('distinct tags.tag_value, tags.id, color_codes.color_code, COUNT(products.id) as amount '));
          return $query->get();
        break;
        case 'search':
        break;
      }
    }
    
    public static function getValuesByTagName($tag_name, $filter_source, $filter_source_id, $filters){

        if (isset($filters['tag']) && count($filters['tag']) > 0){
            $used_tags = self::getUsedTagsFromArray($filters['tag']);

            if (count($used_tags) > 0){

                if (is_array($filters['tag'])){

                    $filters['tag'] = array_filter($filters['tag'], function($v) use($used_tags, $tag_name){
                        if ($used_tags[$v] !== $tag_name){
                            return $v;
                        }
                    });

                } else {
                    if ($used_tags[$filters['tag']] == $tag_name){
                        $filters['tag'] = null;
                    }
                }
            }
        }

        switch($filter_source){
            case 'category_page':

//                dd($filters);

//                $filters['tag'] = null;

                $query = Product::getByCategoryIdQuery(intval($filter_source_id), null, $filters, null);

                $query = $query->join('product_tags', 'products.id', '=', 'product_tags.product_id')
                    ->join('tags', 'product_tags.tag_id', '=', 'tags.id')
                    ->where('tags.tag_name', $tag_name)
                    ->distinct()
                    ->orderBy('tags.tag_value', 'ASC')
                    ->groupBy('tags.tag_value')
                    ->select(\DB::raw('tags.tag_value, tags.id, COUNT(products.id) as amount'));

                return $query->get();

                break;
            case 'search':

                break;
        }

    }
}
