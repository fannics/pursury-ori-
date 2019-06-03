<?php namespace ProjectCarrasco;

use Illuminate\Database\Eloquent\Model;

class ProductProperty extends Model {

	protected $table = 'product_properties';

    public static function getDistinctColorsAndCodes(){

        $sessionCountry = isset($_COOKIE['sessionCountry']) ? $_COOKIE['sessionCountry'] : '';
        $sessionLanguage = isset($_COOKIE['sessionLanguage']) ? $_COOKIE['sessionLanguage'] : '';
        $currentLocale = get_current_locate($sessionCountry,$sessionLanguage);
        $currentLocale = str_replace('/','',$currentLocale);
        $pieces = explode("-", $currentLocale);
        $country = strtolower($pieces[0]);
        $language = strtolower($pieces[1]);

        /*$colors_and_codes = \DB::table('product_properties')
            ->select('product_properties.value', 'color_codes.color_code')
            ->leftJoin('color_codes', 'product_properties.value', '=', 'color_codes.color_name')
            ->where('product_properties.name', 'Color')
            ->where('product_properties.country', $country)
            ->where('product_properties.language', $language)
            ->groupBy('product_properties.value')
            ->paginate(100);*/

        $colors_and_codes2 = \DB::table('tags')
            ->select('tags.tag_value as value', 'color_codes.color_code')
            ->leftJoin('color_codes', 'tags.tag_value', '=', 'color_codes.color_name')
            ->where('tags.tag_name', 'Color')
            ->where('tags.country', $country)
            ->where('tags.language', $language)
            ->groupBy('tags.tag_value')
            ->paginate(100);
        
        return $colors_and_codes2;
    }

}
