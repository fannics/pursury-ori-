<?php namespace ProjectCarrasco;

use Illuminate\Database\Eloquent\Model;

class ColorCodes extends Model {

	protected $table = 'color_codes';

	public static function getIncompleteCount(){
  
    $sessionCountry = isset($_COOKIE['sessionCountry']) ? $_COOKIE['sessionCountry'] : '';
    $sessionLanguage = isset($_COOKIE['sessionLanguage']) ? $_COOKIE['sessionLanguage'] : '';
    $currentLocale = get_current_locate($sessionCountry,$sessionLanguage);
    $currentLocale = str_replace('/','',$currentLocale);
    $pieces = explode("-", $currentLocale);
    $country = strtolower($pieces[0]);
    $language = strtolower($pieces[1]);

		$amount = \DB::table('color_codes')
			->where('color_code', 'LIKE', '%transparent%')
      ->where('country', $country)
      ->where('language', $language)
			->count();

		return $amount;

	}

	public static function getCompleteColorsCount(){

    $sessionCountry = isset($_COOKIE['sessionCountry']) ? $_COOKIE['sessionCountry'] : '';
    $sessionLanguage = isset($_COOKIE['sessionLanguage']) ? $_COOKIE['sessionLanguage'] : '';
    $currentLocale = get_current_locate($sessionCountry,$sessionLanguage);
    $currentLocale = str_replace('/','',$currentLocale);
    $pieces = explode("-", $currentLocale);
    $country = strtolower($pieces[0]);
    $language = strtolower($pieces[1]);

		$amount = \DB::table('color_codes')
			->where('color_code', 'NOT LIKE', '%transparent%')
      ->where('country', $country)
      ->where('language', $language)
			->count();

		return $amount;

	}

}
