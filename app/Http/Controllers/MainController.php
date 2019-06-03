<?php 

  namespace ProjectCarrasco\Http\Controllers;
  use ProjectCarrasco\Category;
  use ProjectCarrasco\ProductCategory;
  use ProjectCarrasco\Brand;
  use ProjectCarrasco\Store;
  use ProjectCarrasco\Setup;
  use ProjectCarrasco\Theme;
  use Illuminate\Http\Request;
  use ProjectCarrasco\Paginator\AppPaginator;
  use ProjectCarrasco\Product;
  use ProjectCarrasco\Tags;
  use ProjectCarrasco\VirtualRouting;
  use Symfony\Component\HttpFoundation\JsonResponse;
  use Symfony\Component\HttpFoundation\Response;

  class MainController extends Controller {

    public function homepageAction(Request $request) {
      $setups = Setup::all();

      if ($request->input('preview')) {
        $referer = $request->header('referer');

        if (strpos($referer, 'admin/homepage') !== false) {
          return view('main/new_homepage', ['preview' => true, 'index' => true, 'setups' => $setups]);
        } 
        else {
          return view('main/new_homepage', ['preview' => false, 'index' => true, 'setups' => $setups]);
        }

      } 
      
      else {
			 return view('main/new_homepage', ['preview' => false, 'index' => true, 'setups' => $setups]);
		  }

    }

    public function index(Request $request, $page = null) {
      $sort_field = $request->input('sorting_field', 'popularity');
      $sort_direction = $request->input('sorting_direction', 'DESC');
      $paginator = Product::paginateForHome($page, 40, $sort_field, $sort_direction);
      $categories = Category::whereNull('parent_id')
        ->where('country',get_current_country())
        ->where('language',get_current_language())
        ->where('is_visible', true)
        ->orderBy('lft', 'ASC')
        ->get();
      return view('main/index', 
        array(
          'products' => $paginator,
          'sorting_field' => $sort_field,
          'sorting_direction' => $sort_direction,
          'categories' => $categories
		    )
      );
	}

	public function productPage(Request $request) {
    $product_id = $request->attributes->get('ref_id');
    $user = \Auth::user();
		
    if ($product_id) {
      $product = Product::find($product_id);
      
      if ($product) {
        
        if ($product->is_visible || (!$product->is_visible && $user && $user->role == 'ROLE_ADMIN')) {
          $app = \App::getFacadeRoot();
					$service = $app['MainService'];
					$service->registerProductView($request, $product);
					$categories = $product->categories;

					if ($categories->count() > 1) {
						$similar_products = Product::similarProducts($product->categories, array(), 1, 20, $product->id);
					} 
          
          else {
            $category = $categories->first();
						$similar_products = Product::paginateByCategory($category->id, $category->parent_id, array(), 1, 20, $product->id);
					}

					return view('main/product', 
            array(
              'product' => $product, 
              'similar_products' => $similar_products
            )
          );
				} 
        
        else {
          abort(404);
				}

			} 
      
      else {
			 abort(404);
			}
      
		} 
    
    else {
			abort(404);
		}
    
  }

	public function postProductPage(Request $request) {
		$product_id = $request->input('product_id');
		$product = Product::find($product_id);
		$product->shop_visits = $product->shop_visits != null ? $product->shop_visits + 1 : 1;
		$product->save();
		return redirect($product->destination_url);
	}

	public function categoryPage(Request $request, $page = null) {
		$category_id = $request->attributes->get('ref_id');

		if ($category_id) {
    	$category = Category::find($category_id);
      
      if (!$category->is_visible) {
        abort(404);
			}

			$filters = $request->input('filters', array());

			if (!$request->input('sorting_field')) {
			 $sortable_fields = Category::sortableFields();
        $key = array_search($category->default_sorting, $sortable_fields);

				if ($key) {
				  $sorting_field = $request->input('sorting_field', $key);
				} 
        
        else {
					$sorting_field = $request->input('sorting_field', 'popularity');
				}

			} 
      
      else {
			 $sorting_field = $request->input('sorting_field', 'popularity');
			}

			$sorting_direction = $request->input('sorting_direction', 'DESC');
			$applicable_filters = $category->getApplicableFilters();
			$available_filters = [];

			if ($applicable_filters) {
        
        foreach ($applicable_filters as $filter) {
          $applicable_values = Category::getApplicableValuesForFilter($category, $filter);
					$available_filters[] = [
					 'filter_name' => $filter,
					 'filter_values' => $applicable_values
					];
				}
        
			}

			$price_range = Product::getPriceRange();
			$brand_filters = Category::getApplicableValuesForProductFilter($category, 'Marca');
      $filters['min_price'] = $request->input('min_price');
			$filters['max_price'] = $request->input('max_price');
			$filters['price_range'] = $request->input('price_range');

			if ($request->input('price_range')) {
        $filters['Precio'] = $request->input('price_range');
			}

			if ($request->input('max_price') && $request->input('min_price')) {
			 
        if (isset($filters['Precio']) && is_array($filters['Precio'])) {
          $filters['Precio'][] = $filters['min_price'].'-'.$filters['max_price'];
				} 
        
        else {
				  $filters['Precio'] = $filters['min_price'].'-'.$filters['max_price'];
				}


			} 
      
      else {
        $min_price = $request->input('min_price');
        $max_price = $request->input('max_price');

				if ($min_price || !$max_price) {
          
          if ($min_price) {
            
            if (isset($filters['Precio']) && is_array($filters['Precio'])) {
              $filters['Precio'][] = $min_price.'-'.$price_range->max_price;
						} 
            
            else {
						  $filters['Precio'] = $min_price.'-'.$price_range->max_price;
						}
					
          }
					
          if($max_price) {
            
            if (isset($filters['Precio']) && is_array($filters['Precio'])) {
              $filters['Precio'][] = $price_range->min_price.'-'.$max_price;
						} 
            
            else {
						  $filters['Precio'] = $price_range->min_price.'-'.$max_price;
						}
					
          }
          
				}

			}

			$paginator = Product::paginateByCategory($category_id, $category->parent_id, $filters, $page, 40, null,$sorting_field, $sorting_direction);
      
			if ($paginator->lastPage() < $page) {
				$params = array_merge($request->input(), array('page' => 1));
  			return redirect(route(\Route::currentRouteName(), $params));
			}

  		return view('main/category', 
        array(
				  'category' => $category,
				  'products' => $paginator,
				  'filters' => $filters,
				  'sorting_field' => $sorting_field,
				  'sorting_direction' => $sorting_direction,
				  'available_filters' => $available_filters,
				  'price_range' => $price_range,
				  'brand_filters' => $brand_filters,
			 )
      );

		} 
    
    else {
			abort(404);
		}
    
	}

	public function renderFilter(Request $request, $category_id, $filter_name) {
    $applicable_values = Category::getApplicableValuesForFilter($category_id, $filter_name);
		return view('main/category_filter', array('values' => $applicable_values));
	}

	public function renderProductFilter(Request $request, $category_id, $filter_name) {

    switch($filter_name) {
      case 'Precio':
        $price_range = Product::getPriceRange();
        return view('main/category_price_filter', array('price_range' => $price_range));
      break;
      case 'Marca':
        $applicable_values = Category::getApplicableValuesForProductFilter($category_id, $filter_name);
				return view('main/category_filter', array('values' => $applicable_values));
			break;
		}
	
  }

	public function renderProductFilterForSearch(Request $request, $filter_name) {
    
    switch($filter_name) {
		  case 'Precio':
        $price_range = Product::getPriceRange();
				return view('main/category_price_filter', array('price_range' => $price_range));
			break;
			case 'Marca':
  		break;
		}
    
	}

	public function testMail(Request $request) {
    die ();
	}

	public function socialLoginRedirect($provider) {
    return \Socialite::with($provider)->redirect();
	}

	public function socialLoginCallbackGoogle(Request $request) {

    if ($request->input('error', null)) {
		  \Session::flash('error', trans('flash.error.session_start_error') );
			return redirect(prefixed_route('/auth/login'));
		} 
    
    else {
      try {
        $social_user = \Socialite::driver('google')->user();
				$user = app('MainService')->handleSocialAuth(
					'google',
					$social_user->getName(),
					$social_user->getEmail(),
					'male',
					$social_user->getAvatar()
				);
				return redirect(route('homepage'));
			} 
      
      catch (\Exception $e) {
				throw $e;
				\Session::flash('error', trans('flash.error.session_start_error2') );
				return redirect(prefixed_route('/auth/login'));
      }

		}
	
  }

	public function socialLoginCallbackFacebook(Request $request) {

    if ($request->input('error', null)) {
      \Session::flash('error', trans('flash.error.session_start_error'));
			return redirect(prefixed_route('/auth/login'));
		} 
    
    else {
      
      try {
        $social_user = \Socialite::driver('facebook')->user();
        $user = app('MainService')->handleSocialAuth(
          'facebook',
					$social_user->getName(),
					$social_user->getEmail(),
					isset($social_user->user) && isset($social_user->user->gender) ? $social_user->user->gender : 'male',
					$social_user->getAvatar()
				);

				return redirect(route('homepage'));

      } 
      
      catch (\Exception $e) {
        \Session::flash('error', trans('flash.error.session_start_error2'));
        return redirect(prefixed_route('/auth/login'));
			}
      
		}
    
	}

	public function searchPage(Request $request, $page = 1) {
    $sorting_field = $request->input('sorting_field', 'popularity');
		$sorting_direction = $request->input('sorting_direction', 'DESC');
		$term = $request->input('term');
		$unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
			'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
			'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
			'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
			'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
		$term = strtr( $term, $unwanted_array );
		$sr = app('MainService')->extendedSearch($term, $page, 30, $sorting_field, $sorting_direction);
		return view('main/searchPage', 
      array(
			 'products' => $sr,
			 'sorting_field' => $sorting_field,
			 'sorting_direction' => $sorting_direction,
			 'term' => $request->input('term')
		  )
    );
	}

	public function searchAutocomplete(Request $request) {
    $service = app('MainService');
		$term = $request->input('term');
		$unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
			'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
			'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
			'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
			'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
    $term = strtr( $term, $unwanted_array );
		$results = $service->searchTerm($term);
		return new JsonResponse($results);
	}

	private function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	private function getRandomPort($available_ports, $last_port) {
    $pos = array_search($last_port, $available_ports);
		
    if ($pos !== false) {
      array_splice($available_ports, $pos, 1);
		}

		return $available_ports[rand(0, count($available_ports) - 1)];
	}

	public function thumborEnd(Request $request, $size) {
		$available_ports = array(8000, 8001, 8002, 8003);
		$last_port = null;
		$new_port = null;

		if (file_exists(storage_path('/app/last.port'))) {
      $last_port = file_get_contents(storage_path('/app/last.port'));
		} 
    
    else {
      $last_port = $available_ports[0];
			file_put_contents(storage_path('/app/last.port'), $last_port);
		}

		if ($last_port) {
      $new_port = $this->getRandomPort($available_ports, $last_port);
		} 
    
    else {
      $new_port = $available_ports[rand(0, count($available_ports) - 1)];
		}

		file_put_contents(storage_path('/app/last.port'), $new_port);
		$image_url = base64_decode($request->input('image_url'));
		$thumbor_address = ends_with(settings('app.thumbor_address'), '/') ? settings('app.thumbor_address') : settings('app.thumbor_address').'/';
		$thumbor_address = str_replace('{port}', $new_port, $thumbor_address);
		$thumbor_url = str_replace('$size', $size, $thumbor_address.'unsafe/$size/');
		$extension = array_reverse(explode('.', $image_url))[0];
		$header_string = '';

		switch($extension) {
      case 'png':
        $header_string = 'image/png';
			break;
			case 'jpg':
			case 'jpe':
			case 'jpeg':
        $header_string = 'image/jpeg';
			break;
			case 'bmp':
        $header_string = 'image/bmp';
			break;
			case 'gif':
        $header_string = 'image/gif';
			break;
			case 'tif':
			case 'tiff':
        $header_string = 'image/tiff';
			break;
		}

		$contents = file_get_contents($thumbor_url.$image_url);

		if ($contents) {
      header('Content-Type: '.$header_string);
			echo ($contents);
		} 
    
    else {
      header('Content-Type: image/png');
			cho (file_get_contents(app_path().'/../public/images/large.png'));
		}
    
	}

	public function fallbackRouteAction(Request $request, $url, $page = null) {

    if (!starts_with($url, '/')) {
      $url = '/'.$url;
		}

    if (starts_with($url, settings('app.route_prefix') )) {
      $url = str_replace(settings('app.route_prefix'), '', $url);
		}

    $setupCollection = Setup::orderBy('country', 'asc')->orderBy('language', 'asc')->get();

    foreach ($setupCollection as $country) {
    
      if ( ($country->country_abre == get_current_country()) && ($country->language_abre == get_current_language()) ) { 
      
        if ($country->default_language == 0) {
        
          if (starts_with($url, '/'.$country->country_abre.'/'.$country->language_abre)) {
  	 		    $url = str_replace( '/'.$country->country_abre.'/'.$country->language_abre, '', $url);
          }
      
        }
      
        else {
            
          if (starts_with($url, '/'.$country->country_abre)) {
  	 		    $url = str_replace( '/'.$country->country_abre, '', $url);
          }
      
        }
    
      }
    
    }

		$alternative_url = $url;

    if (!ends_with($alternative_url, '/')) {
      $alternative_url = $alternative_url . '/';
		}

		$segments = explode('/', $alternative_url);
		$segments = array_filter($segments, 
      function($value) {
        if ($value){
          return $value;
			   }
		  }
    );

		$segments = array_merge(array(), $segments);
		$potential_page = null;

    try {
      if (is_numeric($segments[count($segments) - 1])) {
        $potential_page = $segments[count($segments) - 1];
        if (ends_with($alternative_url, '/'.$potential_page.'/' )) {
          $alternative_url = str_replace('/'.$potential_page.'/', '', $alternative_url);
          if (!ends_with($alternative_url, '/')) {
            $alternative_url = $alternative_url. '/';
          }
        }
        if (ends_with($url, '/'.$potential_page)) {
          $url = substr($url, 0, strlen($url) - strlen('/'.$potential_page));
        }
		  }
    }
    catch (\Exception $e) {
      //do nothing
    }
    
		$route = VirtualRouting::where('country', get_current_country())
      ->where('language', get_current_language())
      ->where(
      
        function ($query) use ($url, $alternative_url) {
          $query->where('route', $url)
            ->orWhere('route', $alternative_url);
        }
        
      )                                 
      ->first();

		if ($route) {
      
      switch($route->route_type) {
        case 'p':
				  $product = Product::find($route->object_id);
					
          if ($product) {
          
            if ($request->isXmlHttpRequest()) {
              return $this->productQuickviewAction($request, $product);
						} 
          
            else {
              $childrenID = null;
              if (isset($_POST["product_id"])) {
                $childrenID = intval($_POST["product_id"]); 
              }
						  return $this->newProductPage($request, $product, $childrenID);
						}
					
          } 
          
          else {
            abort(404);
				  }

				break;
		case 'c':
          $category = Category::find($route->object_id);
          if ($category) {
            return $this->newCategorypage($request, $category, $potential_page);
					} 
					abort(404);
        break;
          case 'b':
              $brand = Brand::find($route->object_id);
			  return $this->brandPage($request,$brand,$potential_page);
          break;
          case 's':
              $store = Store::find($route->object_id);
              return $this->storePage($request,$store,$potential_page);
              break;
      }

		} 
    
    else {
      abort(404);
		}
    
	}

	public function productQuickviewAction(Request $request, $product) {
    $product_data = [
		  'product_id' => $product->id,
			'product_title' => $product->title,
			'image_url' => resized_image($product->image, 'file'),
			'previous_price' => $product->previous_price != 0 ? print_price($product->previous_price, true) : '',
			'price' => print_price($product->price),
      'image_alt' => $product->image_alt
		];
		$similar_products = null;

    if ($request->input('mob') == 'true') {
      $categories = $product->categories;

			if ($categories->count() > 1) {
        $similar_products = Product::paginateByCategoryUsingTags($categories, null, array(), 1, 20, $product->id);
				$similar_products = $similar_products->items();
				$res = [];

				foreach($similar_products as $sim_product) {
				  
          if ($sim_product->price < $sim_product->previous_price) {
            $discount = round(100 - ($sim_product->price * 100 / $sim_product->previous_price ));
						$sim_product->discount = $discount.'%';
					} 
          
          else {
					 $sim_product->discount = '';
					}

					if ($sim_product->previous_price == 0) {
            $sim_product->previous_price = '';
					} 
          
          else {
					 $sim_product->previous_price = print_price($sim_product->previous_price, true);
					}

					$sim_product->price = print_price($sim_product->price);
					$res[] = $sim_product;
				}

				$similar_products = $res;

			} 
      
      else {
				$category = $categories->first();
				$similar_products = Product::paginateByCategoryUsingTags($category->id, $category->parent_id, array(), 1, 20, $product->id);
				$similar_products = $similar_products->items();
				$res = [];

				foreach($similar_products as $sim_product) {
					
          if ($sim_product->price < $sim_product->previous_price) {
            $discount = round(100 - ($sim_product->price * 100 / $sim_product->previous_price ));
						$sim_product->discount = $discount.'%';
					} 
          
          else {
						$sim_product->discount = '';
					}

					if ($sim_product->previous_price == 0) {
            $sim_product->previous_price = '';
					} 
          
          else {
					 $sim_product->previous_price = print_price($sim_product->previous_price, true);
					}

					$sim_product->price = print_price($sim_product->price);
					$res[] = $sim_product;
				}

				$similar_products = $res;
			}

		}

		if ($similar_products) {
      return response()->json(
        [
				  'status' => 'success',
				  'data' => $product_data,
				  'similar_products' => $similar_products
			 ]
      );
		} 
    
    else {
		  return response()->json(
        [
				  'status' => 'success',
				  'data' => $product_data
			 ]
      );
		}
    
	}

  public function newProductPage(Request $request, $product, $childrenID=null) {
    $user = \Auth::user();

		if ($product) {
    
      if ( !empty($childrenID) ) {
        try {
          $tempRecord = Product::where('parent_id', $product->product_id)->where('id',$childrenID)->first();
          if ($tempRecord) {
            $product = $tempRecord;
          } 
  			}
        catch (\Exception $e) {
          //do nothing
        }
      }
      
      if ($product->is_visible || (!$product->is_visible && $user && $user->role == 'ROLE_ADMIN')) {
        $app = \App::getFacadeRoot();
        $service = $app['MainService'];
				$service->registerProductView($request, $product);                                                    
				$categories = $product->categories;

				if ($categories->count() > 1) {
          $similar_products = Product::paginateByCategoryUsingTags($categories, null, array(), 1, 20, $product->id);
				} 
        
        else {
          $category = $categories->first();
					$similar_products = Product::paginateByCategoryUsingTags($category->id, $category->parent_id, array(), 1, 20, $product->id);
				}
        
        $children_products = null;
        $parent_filters = [];
        if ($product->is_parent) {
					$children_products = Product::where('country', get_current_country())
            ->where('language', get_current_language())
            ->where('parent_id',$product->product_id)
            ->get();
          
          foreach ($children_products as $children_product) {
            $aPFilter = explode(";", $children_product->parent_filters);
        
            foreach ($aPFilter as $aFilter) {
              
              if (in_array($aFilter, $parent_filters)) {
                //do nothing
              }
              else {
                array_push($parent_filters, $aFilter);
              }
              
            }
              
          }
          
        }
        
        
        $hrefLangProducts = $this->getHrefLangProducts($product->id);

        $blocks = [];
        if ($product->is_parent && (count($parent_filters) > 1))
        {
            foreach ($children_products as $children_product)
            {

                $blocks [ucfirst(getTagForProduct($children_product->id, $parent_filters[0]))][] = $children_product  ;
            }
             array_shift($parent_filters);
        }



				return view('main/product', 
          array(
            'product' => $product, 
            'similar_products' => $similar_products,
            'hrefLangProducts' => $hrefLangProducts,
            'children_products' => $children_products,
            'parent_filters' => $parent_filters,
              'blocks' => $blocks
          )
        );

			} 
      
      else {
			 abort(404);
			}

		} 
    
    else {
		  abort(404);
		}
    
	}

	private function extractFiltersFromQueryString($query_string) {
    $filters = [];
		$query_string_parts = explode('&',$query_string);

		foreach($query_string_parts as $qs_part) {

      if ($qs_part) {
        $qs_s = explode('=', $qs_part);
				
        if (isset($qs_s[0]) && isset($qs_s[1])) {
        
          if (!isset($filters[$qs_s[0]])) {
            $filters[$qs_s[0]] = urldecode($qs_s[1]);
					} 
        
          else {
            if (!is_array($filters[$qs_s[0]])) {
              $filters[$qs_s[0]] = [ $filters[$qs_s[0]] ];
						}
						$filters[$qs_s[0]][] = urldecode($qs_s[1]);
					}
				
        }

			}

		}

		return $filters;

	}

	private function pushFilter(&$filters, $label, $value) {

		if (!isset($filters[$label])) {
      $filters[$label] = [];
		}

		$filters[$label][] = $value;

	}

	private function getUsedFilters($filters) {

    if ($filters && count($filters) > 0) {
		  $used_filters = [];

			foreach($filters as $key=>$filter) {

				switch($key) {
          case 'brand':
            if (is_array($filter)) {
              foreach ($filter as $filter_elem) {
                $this->pushFilter($used_filters, 'brand', $filter_elem);
							}
						} 
            else {
						  $this->pushFilter($used_filters, 'brand', $filter);
						}
					break;
					case 'price':
						$used_filters['price'] = $filter;
					break;
					case 'tag':
            if (is_array($filter)) {
              foreach ($filter as $filter_elem) {
                $tag = Tags::find($filter_elem);
                if ($tag) {
                  $this->pushFilter($used_filters, $tag->tag_name, 
                    [
										  'tag_id' => $tag->id,
										  'tag_value' => $tag->tag_value
									 ]
                  );
								}
							}
						} 
            else {
						  $tag = Tags::find($filter);
							if ($tag) {
                $this->pushFilter($used_filters, $tag->tag_name, 
                  [
									 'tag_id' => $tag->id,
									 'tag_value' => $tag->tag_value
								  ]
                );
							}
						}
					break;
				}
			
      }
			
      return $used_filters;
		
    } 
    else {
		  return [];
		}
	}

      public function brandPage(Request $request , $brand ,$page = null)
      {
          if (!$brand->is_visible) {
              abort(404);
          }

        switch ($brand->default_sorting)
        {
            case 'Name':
                $productsSort = 'title';
                break;
            case 'Id':
                $productsSort = 'id';
                break;
            default :
                $productsSort = 'title';
                break;
        }
       $catId =  $request->input('category');
       $products = $brand->products()->orderBy(strtolower($productsSort));
       $productsCatsIds = collect([]);

        if (!is_null($catId) && is_numeric($catId))
        {
          $productsCatsIds = ProductCategory::where('category_id','=',$catId)->get()->map(function($item){return $item->product_id;});
          $products = $products->whereIn('id',$productsCatsIds->toArray());

        }
        $itemPerPage = 30;
        $productsCount = $products->count();

         $products = $products->take($itemPerPage)->skip(($page - 1) * $itemPerPage)->get();

        if ($products->isEmpty())
        {
            $products = $brand->products();
            if (!$productsCatsIds->isEmpty())
            {
                $products =  $products->whereIn('id',$productsCatsIds->toArray());
            }
            $page = null;
           $products = $products->take($itemPerPage)->skip(($page - 1) * $itemPerPage)->get();
        }

        $products = new AppPaginator($products,$productsCount,$itemPerPage,$page);

        $productsIds = $brand->products->map(function($item){return $item->id;})->toArray();


        $categoriesIds = ProductCategory::whereIn('product_id',$productsIds)->get()->map(function($item){return $item->category_id;});
        $categoriesIds = array_unique($categoriesIds->toArray());

        $categories = Category::whereIn('id',$categoriesIds)->get();

          return view('main.brand', [
              'brand' => $brand,
              'products' => $products,
              'categories' => $categories
          ]);
      }

      public function storePage(Request $request , $store ,$page = null)
      {
          if (!$store->is_visible) {
              abort(404);
          }


          $catId =  $request->input('category');
          $products = $store->products()->orderBy('title');

          $productsCatsIds = collect([]);

          if (!is_null($catId) && is_numeric($catId))
          {
              $productsCatsIds = ProductCategory::where('category_id','=',$catId)->get()->map(function($item){return $item->product_id;});
              $products = $products->whereIn('id',$productsCatsIds->toArray());

          }
          $itemPerPage = 30;
          $productsCount = $products->count();

          $products = $products->take($itemPerPage)->skip(($page - 1) * $itemPerPage)->get();

          if ($products->isEmpty())
          {
              $products = $store->products();
              if (!$productsCatsIds->isEmpty())
              {
                  $products =  $products->whereIn('id',$productsCatsIds->toArray());
              }
              $page = null;
              $products = $products->take($itemPerPage)->skip(($page - 1) * $itemPerPage)->get();
          }

          $products = new AppPaginator($products,$productsCount,$itemPerPage,$page);

          $productsIds = $store->products->map(function($item){return $item->id;})->toArray();


          $categoriesIds = ProductCategory::whereIn('product_id',$productsIds)->get()->map(function($item){return $item->category_id;});
          $categoriesIds = array_unique($categoriesIds->toArray());

          $categories = Category::whereIn('id',$categoriesIds)->get();

          return view('main.store', [
              'store' => $store,
              'products' => $products,
              'categories' => $categories
          ]);
      }

	public function newCategoryPage(Request $request, $category, $page = null) {
  
		if (!$category->is_visible) {
      abort(404);
		}
		
    if (!$request->input('sort_by')) {
		  $sortable_fields = Category::sortableFields();
			$key = array_search($category->default_sorting, $sortable_fields);
      if ($key) {
				$sorting_field = $request->input('sort_by', $key);
			} 
      else {
				$sorting_field = $request->input('sort_by', 'popularity');
			}
		} 
    else {
		  $sorting_field = $request->input('sort_by', 'popularity');
		}

		$sorting_direction = $request->input('sort_dir', 'DESC');
		$filters = $this->extractFiltersFromQueryString($request->getQueryString());
		$category_filters = explode(';', $category->filters);

		$category_filters = array_map(
      function($value) {
        if ($value) {
				  return trim($value);
        }
		  }, $category_filters
    );

		$used_filters = $this->getUsedFilters($filters);
		$paginator = Product::paginateByCategoryUsingTags($category->id, $category->parent_id, $filters, $page, 40, null,$sorting_field, $sorting_direction);
		if ($paginator->lastPage() < $page) {
      $params = $request->input();
      return redirect(route(\Route::currentRouteName(), array_merge(array('url' => $category->url_key), $params)));
		}

		$category_tree = Category::getCategoryTreeForNavigation($category);
	
    $hrefLangCategories = $this->getHrefLangCategories($category->id);

  	return view('main/category', 
      array(
        'category' => $category,
        'products' => $paginator,
        'filters' => $filters,
        'sorting_field' => $sorting_field,
        'sorting_direction' => $sorting_direction,
        'category_filters' => $category_filters,
        'used_filters' => $used_filters,
        'category_tree' => $category_tree,
        'hrefLangCategories' => $hrefLangCategories
		  )
    );
	}

	public function themeAction(Request $request) {

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
    $language = strtolower($pieces[1]);
    
    if (\Cache::has('theme_definition'.'_'.$country.'_'.$language)) {
      $theme_definition = \Cache::get('theme_definition'.'_'.$country.'_'.$language);
		} 
    
    else {
		  $theme = Theme::where('country', $country)->where('language', $language)->orderBy('created_at','DESC')->take(1)->first();
      
      if (empty($result)) {               
        $response = new Response();
	     	$response->headers->set('Content-Type','text/css');
  		  return '';
      }
      
			$theme_definition = json_decode($theme->data, true);
			\Cache::forever('theme_definition'.'_'.$country.'_'.$language, $theme_definition);
		}

		if ($theme_definition['theme']['active'] == '1') {
      $theme_content = \View::make('main/theme', ['theme' => $theme_definition['theme']])->render();
		} 
    
    else {
      $theme_content = \View::make('main/theme', ['theme' => $theme_definition['theme']])->render();
		  //$theme_content = '';
		}

		$response = new Response($theme_content);
		$response->headers->set('Content-Type','text/css');
		return $response;
	}

	public function getTagsAction(Request $request) {
    $used_filters = $request->input('qs_params', []);
		$filters = $used_filters;
		$filter_source = $request->input('source');
		$filter_source_id = $request->input('source_id');
		
    switch ($request->input('tagName')) {
      case 'Marca':
        $brands = Product::getProductBrands($filter_source, $filter_source_id, $filters);
				$filter_content = \View::make('main/brand_filter_content', ['brands' => $brands])->render();
				return response()->json(['status' => 'success', 'content' => $filter_content]);
			break;
			case 'Precio':
        $price_range = Product::getMatchingPriceRange($filters, $filter_source, $filter_source_id);
				$used_range = isset($filters['price']) ? explode('-', $filters['price']) : array($price_range->min_price, $price_range->max_price);
				$tag_content = \View::make('main/price_tag_content', ['price_range' => $price_range, 'price_filter' => $used_range ])->render();
				return response()->json(['status' => 'success', 'content' => $tag_content, 'contentType' => 'slider']);
      break;
			case 'Color':
        $color_tags = Tags::getColorTags($filter_source, $filter_source_id, $filters);
        $children_color_tags = Tags::getColorTags($filter_source, $filter_source_id, $filters, true);
        $merged_color_tags = array_merge( $color_tags, $children_color_tags );
        $tag_content = \View::make('main/color_tag_content', ['colors' => $merged_color_tags])->render();
				return response()->json(['status' => 'success', 'content' => $tag_content]);
      break;
			default:
        $tag_values = Tags::getValuesByTagName($request->input('tagName'), $filter_source, $filter_source_id, $filters);
				$tag_content = \View::make('main/regular_tag_content', ['tag_values' => $tag_values])->render();
				return response()->json(['status' => 'success', 'content' => $tag_content]);
      break;
		}

	}

	public function onDemandImageResizeAction(Request $request) {
    $image_url = $request->get('url');
		$image_url = urldecode($image_url);
		
    if ($request->input('type') == 'thumbnail') {
      $dimensions = settings('app.thumbnail_size_for_tile', '200x200');
		} 
    
    else {
      $dimensions = settings('app.product_file_image_size', '700x900');
		}

		$dimension_parts = explode('x', $dimensions);
		$desired_width = $dimension_parts[0];
		$desired_height = $dimension_parts[1];
		$ch = curl_init($image_url);
		curl_setopt($ch, CURLOPT_PROXY, 'http://proxy.sld.cu:3128');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$body = curl_exec($ch);
		$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if (!in_array(substr($status_code, 0,1), [4,5])) {
      $image_type = '';
			$image = null;
			$tmp_path = sys_get_temp_dir();
			$tmp_filename = md5($image_url);

			switch ($content_type) {
        case 'image/jpg':
				case 'image/jpeg':
				  $image_type = 'jpg';
					file_put_contents($tmp_path.$tmp_filename.'.'.$image_type, $body);
					$image = imagecreatefromjpeg($tmp_path.$tmp_filename.'.'.$image_type);
				break;
				case 'image/png':
				  $image_type = 'png';
					file_put_contents($tmp_path.$tmp_filename.'.'.$image_type, $body);
					$image = imagecreatefrompng($tmp_path.$tmp_filename.'.'.$image_type);
				break;
				case 'image/wbmp':
  				$image_type = 'bmp';
					file_put_contents($tmp_path.$tmp_filename.'.'.$image_type, $body);
					$image = imagecreatefromwbmp($tmp_path.$tmp_filename.'.'.$image_type);
				break;
				default:
  			break;
		  }

			$new_width = $desired_width;
			$new_height = $desired_height;
			$old_x = imagesx($image);
			$old_y = imagesy($image);

			if ($old_x > $old_y) {
        $thumb_w = $new_width;
				$thumb_h = $old_y*($new_height/$old_x);
			}

			if ($old_x < $old_y) {
        $thumb_w = $old_x*($new_width/$old_y);
				$thumb_h = $new_height;
			}

			if ($old_x == $old_y) {
        $thumb_w =  $new_width;
				$thumb_h = $new_height;
			}

			$dst_image = imagecreatetruecolor($thumb_w, $thumb_h);
			imagecopyresampled($dst_image,$image,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
			
      switch ($image_type) {
        case 'jpg':
				  header('Content-Type: image/jpeg');
					imagejpeg($dst_image);
				break;
				case 'png':
					header('Content-Type: image/png');
					imagejpeg($dst_image);
				break;
				case 'bmp':
					header('Content-Type: image/wbmp');
					imagejpeg($dst_image);
				break;
			}

		} 
    
    else {
		}

		die;

	}
  
  public function getHrefLangProducts ($product_id) {
    $viewingProduct = Product::find($product_id);
    $hrefLangProducts = Product::where('product_id', $viewingProduct->product_id)
      ->get();
    
    $returnArray = [];
    foreach($hrefLangProducts as $hrefLangProduct) {
      $thisSetup = Setup::where('country_abre',$hrefLangProduct->country)
        ->where('language_abre',$hrefLangProduct->language)
        ->first();
      if ($thisSetup->default_language == 1) {
        $hrefLangProduct->language_url = '';
        if ( substr( $hrefLangProduct->url_key, 0, 1 ) === "/") {
          $hrefLangProduct->url_key = substr( $hrefLangProduct->url_key, 1) ;
        }
      }
      else {
        $hrefLangProduct->language_url = $hrefLangProduct->language;
      }
      array_push($returnArray,$hrefLangProduct);
    } 
    
    return $returnArray;      
  }

  public function getHrefLangCategories ($category_id) {
    $viewingCategory = Category::find($category_id);
    $hrefLangCategories = Category::whereNotNull('reference')->where('reference', $viewingCategory->reference)
      ->get();
    
    $returnArray = [];
    foreach($hrefLangCategories as $hrefLangCategory) {
      $thisSetup = Setup::where('country_abre',$hrefLangCategory->country)
        ->where('language_abre',$hrefLangCategory->language)
        ->first();
      if ($thisSetup->default_language == 1) {
        $hrefLangCategory->language_url = '';
        if ( substr( $hrefLangCategory->url_key, 0, 1 ) === "/") {
          $hrefLangCategory->url_key = substr( $hrefLangCategory->url_key, 1) ;
        }
      }
      else {
        $hrefLangCategory->language_url = $hrefLangCategory->language;
      }
      array_push($returnArray,$hrefLangCategory);
    } 
    
    return $returnArray;      
  }

}