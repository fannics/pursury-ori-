<?php namespace ProjectCarrasco;

use Illuminate\Database\Eloquent\Model;
use ProjectCarrasco\Paginator\AppPaginator;

class Translation extends Model {

    protected $table = 'translator_translations';

    public static function paginateForAdmin($paginationFields) {
      return new AppPaginator(
        self::getAllTranslations($paginationFields),
        self::getTranslationsCount($paginationFields),
        $paginationFields['itemsPerPage'],
        ceil($paginationFields['offset'] / $paginationFields['itemsPerPage']) + 1
      );
    }

    public static function getAllTranslations ($paginationFields) {
  
      $sessionCountry = isset($_COOKIE['sessionCountry']) ? $_COOKIE['sessionCountry'] : '';
      $sessionLanguage = isset($_COOKIE['sessionLanguage']) ? $_COOKIE['sessionLanguage'] : '';
      $currentLocale = get_current_locate($sessionCountry,$sessionLanguage);
      $currentLocale = str_replace('/','',$currentLocale);

      $query = \DB::table('translator_translations as t')
            ->where('locale', '=', $currentLocale)
            ->select(\DB::raw('t.id, t.group, t.item, t.text'))
            ->skip($paginationFields['offset'])
            ->take($paginationFields['itemsPerPage']);
            
      foreach ($paginationFields['sorting'] as $sort_field) {
        switch($sort_field['field']) {
          case 'group':
            $query->orderBy('t.group', $sort_field['dir']);
          break;
          case 'items':
            $query->orderBy('t.item', $sort_field['dir']);
          break;
          case 'text':
            $query->orderBy('t.text', $sort_field['dir']);
          break;
        }
      }

      if (isset($paginationFields['filters']) && count($paginationFields['filters']) > 0) {
        $query->where(
          function($query) use ($paginationFields) {
            $query->orWhere('t.group', 'like', '%'.$paginationFields['filters'].'%')
                ->orWhere('t.item', 'like', '%'.$paginationFields['filters'].'%')
                ->orWhere('t.text', 'like', '%'.$paginationFields['filters'].'%');
          }
        );
      }

      $translations = $query->get();

      $translation_final = $translations;

      return $translation_final;
    }

    public static function getTranslationsCount($paginationFields = null) {
      $sessionCountry = isset($_COOKIE['sessionCountry']) ? $_COOKIE['sessionCountry'] : '';
      $sessionLanguage = isset($_COOKIE['sessionLanguage']) ? $_COOKIE['sessionLanguage'] : '';
      $currentLocale = get_current_locate($sessionCountry,$sessionLanguage);
      $currentLocale = str_replace('/','',$currentLocale);
      $query = \DB::table('translator_translations as t')->where('locale', '=', $currentLocale);
      return $query->count();
    }
    
    public static function forExport(){
        return \DB::select('SELECT `group`, `item`, `text`
                            FROM translator_translations c WHERE locale="en" ORDER BY `group` ASC');
    }

}