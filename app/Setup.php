<?php namespace ProjectCarrasco;

use Illuminate\Database\Eloquent\Model;
use ProjectCarrasco\Paginator\AppPaginator;

class Setup extends Model {
  
  protected $table = 'setups';
  protected $fillable = ['country','country_abre','language','default_language','language_abre',
                          'currency', 'currency_symbol','before_after','currency_decimal','default_setup'
                        ];

  public static function paginateForAdmin($paginationFields){
    return new AppPaginator(
      self::getAllSetups($paginationFields),
      self::getSetupsCount($paginationFields),
      $paginationFields['itemsPerPage'],
      ceil($paginationFields['offset'] / $paginationFields['itemsPerPage']) + 1
    );
  }

  public static function getAllSetups ($paginationFields) {
    $query = \DB::table('setups as s')
            ->select(\DB::raw('s.id, s.country, s.country_abre, s.language, s.default_language, s.language_abre, s.currency, s.currency_symbol, s.before_after, s.currency_decimal, s.default_setup'))
            ->skip($paginationFields['offset'])
            ->take($paginationFields['itemsPerPage']);
            

    foreach ($paginationFields['sorting'] as $sort_field) {
      switch($sort_field['field']) {
        case 'country':
          $query->orderBy('s.country', $sort_field['dir']);
        break;
        case 'country_abre':
          $query->orderBy('s.country_abre', $sort_field['dir']);
        break;
        case 'language':
          $query->orderBy('s.language', $sort_field['dir']);
        break;
        case 'default_language':
          $query->orderBy('s.default_language', $sort_field['dir']);
        break;
        case 'language_abre':
          $query->orderBy('s.language_abre', $sort_field['dir']);
        break;
        case 'currency':
          $query->orderBy('s.currency', $sort_field['dir']);
        break;
        case 'currency_symbol':
          $query->orderBy('s.currency_symbol', $sort_field['dir']);
        break;
        case 'before_after':
          $query->orderBy('s.before_after', $sort_field['dir']);
        break;
        case 'currency_decimal':
          $query->orderBy('s.currency_decimal', $sort_field['dir']);
        break;
      }
    }

    if (isset($paginationFields['filters']) && count($paginationFields['filters']) > 0) {
      $query->where(
        function($query) use ($paginationFields) {
          $query->orWhere('s.country', 'like', '%'.$paginationFields['filters'].'%')
                ->orWhere('s.country_abre', 'like', '%'.$paginationFields['filters'].'%')
                ->orWhere('s.language', 'like', '%'.$paginationFields['filters'].'%')
                ->orWhere('s.default_language', 'like', '%'.$paginationFields['filters'].'%')
                ->orWhere('s.language_abre', 'like', '%'.$paginationFields['filters'].'%')
                ->orWhere('s.currency', 'like', '%'.$paginationFields['filters'].'%')
                ->orWhere('s.currency_symbol', 'like', '%'.$paginationFields['filters'].'%')
                ->orWhere('s.before_after', 'like', '%'.$paginationFields['filters'].'%')
                ->orWhere('s.currency_decimal', 'like', '%'.$paginationFields['filters'].'%');
        }
      );
    }

    $setups = $query->get();

    $setups_final = collect();
    foreach ($setups as $setup) {
    
      switch ($setup->default_language) {
        case "0":
          $setup->default_language = 'no';
        break;
        case "1":
          $setup->default_language = 'yes';
        break;
      }
      
      switch ($setup->before_after) {
        case "0":
          $setup->before_after = 'before';
        break;
        case "1":
          $setup->before_after = 'after';
        break;
      }

      switch ($setup->default_setup) {
        case "0":
          $setup->default_setup = 'no';
        break;
        case "1":
          $setup->default_setup = 'yes';
        break;
      }

      $setups_final->push($setup);
      
    }

    return $setups_final;
  }

  public static function getSetupsCount($paginationFields = null) {
    $query = \DB::table('setups as s');
    return $query->count();
  }


}
