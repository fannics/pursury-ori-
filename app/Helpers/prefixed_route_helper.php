<?php

  use ProjectCarrasco\Product;
  use ProjectCarrasco\ProductTag;
  use ProjectCarrasco\Setup;
  use ProjectCarrasco\Tags;
 
  use Waavi\Translation\Models\Language;
                                                                                                                     
  function get_path_for_front() {
    $country = get_current_country();
    $language = get_current_language();
    try {
      $thisSetup = Setup::where('country_abre', $country)->where('language_abre', $language)->first();
      if ($thisSetup->default_language == 1) {
        return '/'.$thisSetup->country_abre;
      }
      else {
        return '/'.$thisSetup->country_abre.'/'.$thisSetup->language_abre;
      }
    }
    catch (Exception $e) {
      return '';    
    }
  }
  
  function get_current_country() {
    $localeArray = get_current_prefixes();
    if ( !isset($localeArray[0]) ) {
      $localeArray[0] = '';
    }
    if ( !isset($localeArray[1]) ) {
      $localeArray[1] = '';
    }
    $currentLocale = get_current_locate($localeArray[0],$localeArray[1]);
    $currentLocale = str_replace('/','',$currentLocale);
    $pieces = explode("-", $currentLocale);
    $country = strtolower($pieces[0]);  
    return $country;
  }
  
  function get_current_language() {
    $localeArray = get_current_prefixes();
    if ( !isset($localeArray[0]) ) {
      $localeArray[0] = '';
    }
    if ( !isset($localeArray[1]) ) {
      $localeArray[1] = '';
    }
    $currentLocale = get_current_locate($localeArray[0],$localeArray[1]);
    $currentLocale = str_replace('/','',$currentLocale);
    $pieces = explode("-", $currentLocale);
    $language = strtolower($pieces[1]);
    return $language;
  }
  
  function get_current_locate($currentCountry, $currentLanguage) {
    if ( empty($currentCountry) ) {
      $defaultSetup = Setup::where('default_setup', 1)->first();
      return strtoupper($defaultSetup->country_abre).'-'.strtolower($defaultSetup->language_abre);  
    }
    else {
      $currentCountry = str_replace('/','',$currentCountry);
      if ( empty($currentLanguage) ) {
        $setupCollection = Setup::orderBy('country', 'asc')->orderBy('language', 'asc')->get();
        foreach ($setupCollection as $country) {
          if (strtolower($country->country_abre)==strtolower($currentCountry)) {
            if ($country->default_language == 1) {
              $currentLanguage = $country->language_abre;
              break;
            }             
          }
          else {
            $currentLanguage = str_replace('/','',$currentLanguage);
          }
        }
      }
      return strtoupper($currentCountry).'-'.strtolower($currentLanguage);  
    }
  }
                                                            
  function get_current_prefixes($fromRoutes = false) {     
    // Init
    $countryFound = false;                                       
    $languageFound = false;
    $returnArray = [];
    $setupCollection = Setup::orderBy('country', 'asc')->orderBy('language', 'asc')->get();
    $defaultSetup = Setup::where('default_setup', 1)->first();
    $urlApp = rtrim( Config::get('app')['url'], '/' );
    try {
      $thisURL = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    }
    catch (Exception $e) {
      if ($fromRoutes) {
        setcookie('sessionCountry', '', time() + (86400 * 30), "/");
        setcookie('sessionLanguage', '', time() + (86400 * 30), "/");
      }
      view()->share('thisCountry', '');
      view()->share('thisLanguage', '');
      \Config::set('app.locale',get_current_locate('',''));
      return $returnArray;
    }
    $thisURL = str_replace($urlApp,'',$thisURL);
    
    $exploded = explode("/", $thisURL);
    $positionExplodedCountry = 1;
    $positionExplodedLanguage = 2;

    // Default setup is mandatory. So, if there is no default setup, the system 
    // set the first record on setup table as default automatically. The sytem 
    // is installed with a default setup (US-en). If all records from setup 
    // table are deleted, a record for US-en combination will be re-created
    // automatically.
    if ( !isset($defaultSetup->country_abre) ) {
      $defaultSetup = $setupCollection->first();
      if ( !isset($defaultSetup->country_abre) ) { 
        $setup = new Setup;
        $setup->country = 'United States';
        $setup->country_abre = 'us';      
        $setup->language = 'English';      
        $setup->default_language = 1;
        $setup->language_abre = 'en';
        $setup->currency = 'USD';
        $setup->currency_symbol = '$';
        $setup->before_after = 0;
        $setup->currency_decimal = 'point';
        $setup->default_setup = 1;
        $setup->save();
        
        $language = Language::where('locale', strtoupper($setup->country_abre).'-'.strtolower($setup->language_abre))->first();
        if (null !== $language) {
          $language->delete();
        } 
        $language = Language::where('name', strtoupper($setup->country).'-'.strtolower($setup->language))->first();
        if (null !== $language) {
          $language->delete();
        }
        $language_new = new Language;
        $language_new->locale = strtoupper($setup->country_abre).'-'.strtolower($setup->language_abre);
        $language_new->name = strtoupper($setup->country).'-'.strtolower($setup->language);
        $language_new->save();
        
        $defaultSetup = $setupCollection->first();          
      }
      else {
        $defaultSetup->default_setup = 1; 
        $defaultSetup->save();
        $defaultSetup = $setupCollection->first();
      }        
    }
    
    // Bypass ULR: auth/login
    if (strpos($_SERVER['REQUEST_URI'],'auth/login') !== false) {
      if ( ($exploded[1] != 'auth') && ($exploded[2] != 'login') ) {
        header("Location: ".rtrim( Config::get('app')['url'], '/' ).'/auth/login');
        exit;
      }
      else {
        $sessionCountry = isset($_COOKIE['sessionCountry']) ? $_COOKIE['sessionCountry'] : '';
        $sessionLanguage = isset($_COOKIE['sessionLanguage']) ? $_COOKIE['sessionLanguage'] : '';
        view()->share('thisCountry', $sessionCountry );
        view()->share('thisLanguage', $sessionLanguage );
        \Config::set('app.locale',get_current_locate($sessionCountry,$sessionLanguage));
        return $returnArray;
      }
    }

    // Bypass URL: not-found-url
    if (strtok(end($exploded),'?')=='not-found-url') {
      if ($fromRoutes) {
        setcookie('sessionCountry', '', time() + (86400 * 30), "/");
        setcookie('sessionLanguage', '', time() + (86400 * 30), "/");
      }
      view()->share('thisCountry', '');
      view()->share('thisLanguage', '');
      \Config::set('app.locale',get_current_locate('',''));
      return $returnArray;
    }

    // Verify country via URL
    $returnArray[0] = null;
    if ( (empty($exploded[$positionExplodedCountry])) || (empty(strtok($exploded[$positionExplodedCountry],'?'))) || (strlen(strtok($exploded[$positionExplodedCountry],'?'))!=2) ) {
      $sessionCountry = isset($_COOKIE['sessionCountry']) ? $_COOKIE['sessionCountry'] : '';
      $sessionLanguage = isset($_COOKIE['sessionLanguage']) ? $_COOKIE['sessionLanguage'] : '';
      if ( !empty($sessionCountry) ) {
        if ( empty($sessionLanguage) ) {
          $redirectURL = $urlApp.$sessionCountry.'/';
        }
        else {
          $redirectURL = $urlApp.$sessionCountry.$sessionLanguage.'/';
        }
        for($i = $positionExplodedCountry; $i < sizeof($exploded); $i++) {
          $redirectURL = $redirectURL . $exploded[$i].'/'; 
        }
        $redirectURL = rtrim($redirectURL, "/");
        header("Location: ".$redirectURL);
        exit;
      }
      else if ( isset($defaultSetup->country_abre) ) {
        if ( empty($defaultSetup->language_abre) ) {
          $redirectURL = $urlApp.'/'.$defaultSetup->country_abre.'/';
        }
        else {
          $redirectURL = $urlApp.'/'.$defaultSetup->country_abre.'/'.$defaultSetup->language_abre.'/';
        }
        for($i = $positionExplodedCountry; $i < sizeof($exploded); $i++) {
          $redirectURL = $redirectURL . $exploded[$i].'/'; 
        }
        $redirectURL = rtrim($redirectURL, "/");
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ".$redirectURL);
        exit;
      } 
      else { 
         header("Location: /not-found-url");
         exit;
     }
    }
    else {
      foreach ($setupCollection as $country) {
        if (strtolower($country->country_abre)==strtolower(strtok($exploded[$positionExplodedCountry],'?'))) {
          $returnArray[0] = strtok($exploded[$positionExplodedCountry],'?');
          $countryFound = true;
          break;
        }
      }
      if (!$countryFound) {
         header("Location: /not-found-url");
         exit;
      }
    }

    // Verify language via URL
    $returnArray[1] = null;
    if ( (empty($exploded[$positionExplodedLanguage])) ||  (empty(strtok($exploded[$positionExplodedLanguage],'?'))) || (strlen(strtok($exploded[$positionExplodedLanguage],'?'))!=2) ) {
      $hasDefaultLanguage = false;
      foreach ($setupCollection as $country) {
        if (strtolower($country->country_abre)==strtolower(strtok($exploded[$positionExplodedCountry],'?'))) {
          if ($country->default_language == 1) {
            $hasDefaultLanguage = true;
            break;
          }             
          }
      }
      if (!$hasDefaultLanguage) {
        header("Location: /not-found-url");
        exit;
      }
    }
    else {
      foreach ($setupCollection as $country) {
        if (strtolower($country->country_abre)==strtolower(strtok($exploded[$positionExplodedCountry],'?'))) {
          if (strtolower($country->language_abre)==strtolower(strtok($exploded[$positionExplodedLanguage],'?'))) {
            if ($country->default_language == 1) {
              $redirectURL = $urlApp.'/'.$exploded[1].'/';
              for($i = $positionExplodedLanguage + 1; $i < sizeof($exploded); $i++) {
                $redirectURL = $redirectURL . $exploded[$i].'/'; 
              }
              $redirectURL = rtrim($redirectURL, "/");
              header("HTTP/1.1 301 Moved Permanently"); 
              header("Location: ".$redirectURL);
              exit; 
            }
            else {
              $returnArray[1] = strtok($exploded[$positionExplodedLanguage],'?');
            }
            $languageFound = true;
            break;
          }
        }
      }
      if (!$languageFound) {
         header("Location: /not-found-url");
         exit;
      }
    }
    try {
      // Set country and language for views
      if ( empty($returnArray[0]) ) {
        if ($fromRoutes) {
          setcookie('sessionCountry', '', time() + (86400 * 30), "/");
        }
        view()->share('thisCountry', '');
      }
      else {
        if ($fromRoutes) {
          setcookie('sessionCountry', '/'.$returnArray[0], time() + (86400 * 30), "/");
        }
        view()->share('thisCountry', '/'.$returnArray[0]);
      }
      if ( empty($returnArray[1]) ) {
        if ($fromRoutes) {
          setcookie('sessionLanguage', '', time() + (86400 * 30), "/");
        }
        view()->share('thisLanguage', '');
      }
      else {
        if ($fromRoutes) {
          setcookie('sessionLanguage', '/'.$returnArray[1], time() + (86400 * 30), "/");
        }
        view()->share('thisLanguage', '/'.$returnArray[1]);
      }
    } 
    catch (\Exception $e) {
    }

    \Config::set('app.locale',get_current_locate($returnArray[0],$returnArray[1]));
    return $returnArray;
  }
  
  function prefixed_route($url) {
    if (strpos($url,'auth/login') !== false) {
      $prefix = \Config::get('app')['route_prefix'];
    }
    else {
      $prefix = \Config::get('app')['route_prefix'].get_path_for_front();
    }
    return $prefix.$url;
  }
  
  function get_route_product($product_id) {
    if (!empty($product_id)) {
      try {
        $theProduct = Product::where('country', get_current_country())->where('language', get_current_language())->where('product_id',intval($product_id))->first();
        return prefixed_route($theProduct->url_key);
      }
      catch (Exception $e) {
        return $e->getMessage();    
      }
    }
    else {
        return '';    
    }
  }

  function from_db_date($date_str, $format) {
    \Jenssegers\Date\Date::setLocale('es');
    $date = \Jenssegers\Date\Date::createFromFormat('Y-m-d H:i:s', $date_str);
    return $date->diffForHumans();
  }

  function truncate_str($str, $length = 25) {
    return strlen($str) < $length ? $str : substr($str, 0, $length).'...';
  }

  function cache_handle() {
    return settings('app.debug') == true ? '?'.time() : '';
  }

  function resized_image($original_image, $image_class = 'thumbnail', $image_size = null) {
    switch (settings('app.image_processor')) {
      case 'thumbor':
        $url_pattern = env('THUMBOR').'/unsafe/{size}/smart/{image_url}';
        if (!$image_size) {
          $size = $image_class == 'thumbnail' ? settings('app.thumbnail_size_for_tile') : settings('app.product_file_image_size');
        } 
        else {
          $size = $image_size;
        }
        $full_url = str_replace('{image_url}', $original_image, $url_pattern);
        $full_url = str_replace('{size}', $size, $full_url);
        return $full_url;
      break;
      case 'Images.weserv.nl':
        $base_url = '//images.weserv.nl/';
        if (!$image_size) {
          $size = $image_class == 'thumbnail' ? settings('app.thumbnail_size_for_tile') : settings('app.product_file_image_size');
          $size = explode('x',$size);
        } 
        else {
          $size = $image_size;
        }
        $original_image = str_replace('https://', '', $original_image);
        $original_image = str_replace('http://', '', $original_image);
        return $base_url.'?url='.$original_image.'&w='.$size[0].'&h='.$size[1];
      break;
      case 'default':
        $url = route('internal_on_demand_image_resizer', 
          [
            'type' => $image_class,
            'url' => urlencode($original_image)
          ]
        );
        return $url;
      break;
      default:
        $url = route('internal_on_demand_image_resizer', 
          [
            'type' => $image_class,
            'url' => urlencode($original_image)
          ]
        );
        return $url;
      break;
    }
  }

  function extract_dimensions($dimensions) {
    return explode('x', $dimensions);
  }

  function lintUrl($url) {
    return addslashes($url);
  }

  function walk_category_tree($categories, $options) {
    $content = '';
    foreach($categories as $cat) {
      if (isset($cat->children) && count($cat->children) > 0) {
        $content .= '<li data-cid="'.$cat->id.'"><a href="'.prefixed_route($cat->url_key).'">'.$cat->title.'</a><ul>'.walk_category_tree($cat->children, $options).'</ul></li>';
      } 
      else {
        $content .= '<li data-cid="'.$cat->id.'"><a href="'.prefixed_route($cat->url_key).'">'.$cat->title.'</a></li>';
      }
    }
    return $content;
  }

  function category_menu_tree($categories, $options = null) {
    return'<ul>'.walk_category_tree($categories, $options).'</ul>';
  }

  function translateLogLevelImage($image) {
    switch($image) {
      case 'info':
        return 'fa fa-info-circle';
      break;
      case 'warning':
        return 'fa fa-exclamation-circle';
      break;
    }
  }

  function sass_darken($hex, $percent) {
    preg_match('/^#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', $hex, $primary_colors);
    str_replace('%', '', $percent);
    $color = "#";
    for ($i = 1; $i <= 3; $i++) {
      $primary_colors[$i] = hexdec($primary_colors[$i]);
      $primary_colors[$i] = round($primary_colors[$i] * (100-($percent*2))/100);
      $color .= str_pad(dechex($primary_colors[$i]), 2, '0', STR_PAD_LEFT);
    }
    return $color;
  }

  function sass_lighten($hex, $percent) {
    preg_match('/^#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', $hex, $primary_colors);
    str_replace('%', '', $percent);
    $color = "#";
    for($i = 1; $i <= 3; $i++) {
      $primary_colors[$i] = hexdec($primary_colors[$i]);
      $primary_colors[$i] = round($primary_colors[$i] * (100+($percent*2))/100);
      $color .= str_pad(dechex($primary_colors[$i]), 2, '0', STR_PAD_LEFT);
    }
    return $color;
  }

  function tag_list($used_tags) {
    $tags = [];
    foreach($used_tags as $tag) {
      $tags[] = $tag['tag_value'];
    }
    return ' '.implode(', ', $tags);
  }
  
  function getTagForProduct($productID, $tag) {
    try {
      $productTags = ProductTag::where('product_id', $productID)->get();
      $tagsArray = []; 
      foreach ($productTags as $productTag) {
        array_push($tagsArray,$productTag->tag_id);
      }
      $theTag = Tags::whereIn('id', $tagsArray)
        ->whereRaw( 'LOWER(`tag_name`) = ?', array( strtolower(trim($tag)) ) )
        ->first();
      return $theTag->tag_value;
    } 
    catch (Exception $e) {
      return null;    
    }
  }

  function print_price($price, $omit_currency = false) {
    $country = get_current_country();
    $language = get_current_language();
    $thisSetup = Setup::where('country_abre', $country)->where('language_abre', $language)->first();
    $thousand_sep = null;
    switch ($thisSetup->currency_decimal) {
      case 'point':
        $decimal_sep = '.';
        $thousand_sep = ',';
      break;
      case 'comma':
        $decimal_sep = ',';
        $thousand_sep = '.';
      break;
      default:
        $decimal_sep = '.';
        $thousand_sep = ',';
      break;
    }
    $number = number_format($price, settings('app.money_decimal_digits'), $decimal_sep, $thousand_sep );
    if (!$omit_currency) {
      switch ($thisSetup->before_after) {
        case 0:
          return htmlentities($thisSetup->currency_symbol).' '.$number;
        break;
        case 1:
          return $number.' '.htmlentities($thisSetup->currency_symbol);
        break;
      }
    } 
    else {
      return $number;
    }
  }

  function etrans($id = null, $parameters = [], $domain = 'messages', $locale = null) {
    if (settings('app.fetch_translations') == true) {
      $base_catalog = \ProjectCarrasco\TranslationCatalog::where('catalog_code', settings('app.base_lang'))->first();
      $trans_key = \ProjectCarrasco\Translation::where('source', $id)->where('catalog_id', $base_catalog->id)->first();
      if (!$trans_key) {
        $trans_key = new \ProjectCarrasco\Translation();
        $trans_key->source = $id;
        $trans_key->catalog_id = $base_catalog->id;
        $trans_key->domain = $domain;
        $trans_key->save();
      }
    }
    return trans($id, $parameters, $domain, $locale);
  }

  function etrans_choice($id, $number, $parameters = [], $domain = 'messages', $locale = null) {
    if (settings('app.fetch_translations') == true) {
      $base_catalog = \ProjectCarrasco\TranslationCatalog::where('catalog_code', settings('app.base_lang'))->first();
      $trans_key = \ProjectCarrasco\Translation::where('source', $id)->where('catalog_id', $base_catalog->id)->first();
      if (!$trans_key) {
        $trans_key = new \ProjectCarrasco\Translation();
        $trans_key->source = $id;
        $trans_key->catalog_id = $base_catalog->id;
        $trans_key->domain = $domain;
        $trans_key->save();
      }
    }
    return trans_choice($id, $number, $parameters, $domain, $locale);
  }
  
?>