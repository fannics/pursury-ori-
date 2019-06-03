<?php namespace ProjectCarrasco\Http\Controllers;
use ProjectCarrasco\Category;
use ProjectCarrasco\Http\Requests;
use ProjectCarrasco\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ProjectCarrasco\Import;
use ProjectCarrasco\MenuConfiguration;
use ProjectCarrasco\Product;
use ProjectCarrasco\TermSearch;
use ProjectCarrasco\User;
use Psy\Util\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
class AdminController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//get the totals
		$total_categories = \DB::table('categories')->where('country', get_current_country() )->where('language', get_current_language() )->count();
		$total_products = \DB::table('products')->where('country', get_current_country() )->where('language', get_current_language() )->count();
		$total_users = \DB::table('users')->count();
		$total_imports = \DB::table('imports')->count();
		$paginator = Product::getMostSeenProducts(1, 20);
		$latest_users = User::paginateForAdmin([
			'itemsPerPage' => 10,
			'offset' => 0,
			'sorting' => [
				[
					'field' => 'created_at',
					'dir' => 'desc'
				]
			]
		]);
		return view('admin/index', [
			'total_categories' => $total_categories,
			'total_products' => $total_products,
			'total_users' => $total_users,
			'total_imports' => $total_imports,
			'most_seen' => $paginator,
			'latest_users' => $latest_users
		]);
	}
	public function menuConfiguration(Request $request){
		$menu_items = MenuConfiguration::query()->orderBy('order', 'ASC')->get();
		$categories = Category::where('country',get_current_country())->where('language',get_current_language())->get();  
		$menu_definition = array();
		foreach($menu_items as $menu_item){
			$category = $menu_item->category;
			$element = array(
				'title' => $category->title,
				'order' => $menu_item->order,
				'url' => prefixed_route($category->url_key),
				'id' => $menu_item->id,
				'display_children' => $menu_item->display_children
			);
			if ($menu_item->display_children && $category->children->count() > 0){
				$children = array();
				foreach($category->children as $child){
					$children[] = array(
						'title' => $child->title,
						'url' => prefixed_route($child->url_key),
						'id' => $child->id
					);
				}
				$element['children'] = $children;
			}
			$menu_definition[] = $element;
		}
		return view('admin/menu_configuration', ['menu_definition' => $menu_definition, 'menu_definition_json' => json_encode($menu_definition), 'categories' => $categories]);
	}
	public function handleMenuConfiguration(Request $request){
		switch($request->input('action')){
			case 'add':
				$category = $request->input('category');
				$existent = MenuConfiguration::query()->where('category_id', $category)->first();
				if ($existent){
					return new JsonResponse(array(
						'status' => 'fail',
						'message' => trans('json.itemAlreadyExists')
					));
				} else {
					$item = new MenuConfiguration();
					$item->category_id = $category;
					$item->display_children = $request->input('display_children') == '1' ? true : false;
					$item->order = $request->input('index');
					$item->save();
					$category = Category::find($category);
					$result = array(
						'id' => $item->id,
						'title' => $category->title,
						'url' => prefixed_route($category->url_key),
						'order' => $item->order,
						'display_children' => $item->display_children
					);
					if ($item->display_children){
						$children = array();
						foreach($category->children as $child){
							$children = array(
								'title' => $child->title,
								'url' => prefixed_route($child->url_key),
								'id' => $child->id
							);
						}
					}
					app('MainService')->updateMenuConfigurationCache();
					return new JsonResponse(array(
						'status' => 'success',
						'menu_item' => $result
					));
				}
			break;
			case 'update':
				//to update positions and children display status
				if ($request->input('items')){
					$data = $request->input('items');
					foreach($data as $item){
						$menu_item = MenuConfiguration::find($item['id']);
						$menu_item->order = $item['position'];
						$menu_item->display_children = $item['display_children'] == 1 ? true : false;
						$menu_item->save();
					}
					app('MainService')->updateMenuConfigurationCache();
					return new JsonResponse(array(
						'status' => 'success'
					));
				} else {
					return new JsonResponse(array(
						'status' => 'success'
					));
				}
				break;
			case 'remove':
				$menu_item = MenuConfiguration::find($request->input('id'));
				if ($menu_item){
					$menu_item->delete();
					app('MainService')->updateMenuConfigurationCache();
					return new JsonResponse(array(
						'status' => 'success',
					));
				} else {
					return new JsonResponse(array(
						'status' => 'fail',
						'message' => trans('json.itemdoesntexist')
					));
				}
				break;
		}
	}
	public function importsAction($page = 1){
		$imports = Import::paginatedForAdmin($page, 10);
		return view('admin/imports', array('imports' => $imports));
	}
	public function exportsAction(){
	}
	public function feedAction($type, $feed){
		$feed = urldecode($feed);
		switch($type){
			case 'categories':
				if (file_exists(storage_path('app/feeds/categories').DIRECTORY_SEPARATOR.$feed)){
					header('Content-Type: text/csv');
					header('Content-Disposition: attachment; filename="'.$feed.'";');
					header("Cache-Control: no-store, no-cache");
					readfile(storage_path('app/feeds/categories').DIRECTORY_SEPARATOR.$feed);
				} else {
					\Session::flash('error', trans('flash.error.no_file_download'));
					return redirect(route('admin_imports_done'));
				}
				break;
			case 'products':
				if (file_exists(storage_path('app/feeds/products').DIRECTORY_SEPARATOR.$feed)){
					header('Content-Type: text/csv');
					header('Content-Disposition: attachment; filename="'.$feed.'";');
					header("Cache-Control: no-store, no-cache");
					readfile(storage_path('app/feeds/products').DIRECTORY_SEPARATOR.$feed);
				} else {
					\Session::flash('error', trans('flash.error.no_file_download'));
					return redirect(route('admin_imports_done'));
				}
				break;
			default:
				\Session::flash('error', trans('flash.error.document_type_unsupported'));
				return redirect(route('admin_imports_done'));
				break;
		}
	}
	public function importLogFileAction(Request $request, $id){
		$import = Import::find($id);
		$log_path = storage_path('logs/') . $import->log_file;
		if (file_exists($log_path)){
			header('Content-Type: text/plain');
			header('Content-Disposition: attachment; filename="'.$import->log_file.'";');
			header("Cache-Control: no-store, no-cache");
			readfile($log_path);
			die ();
		}
		abort(404);
	}
	public function optimizeAction(){
		return view('admin/optimize');
	}
	public function postOptimizeAction(Request $request){
		set_time_limit(0);
		try{
			$command = $request->input('command');
			if (strpos($command, ' ') !== false){
				$parts = explode(' ', $command);
				$command_name = $parts[0];
				$arguments = [];
				foreach($parts as $p){
					if ($p !== $command_name){
						if (strpos($p, '=') !== false){
							$p_parts = explode('=', $p);
							$arguments[$p_parts[0]] = $p_parts[1];
						} else {
							$arguments[$p] = true;
						}
					}
				}
				$exit_code = \Artisan::call($command_name, $arguments);
			} else {
				$exit_code = \Artisan::call($request->input('command'));
			}
			return response()->json(['status' => 'success', 'message' => 'allok', 'exit_code' => $exit_code]);
		} catch (\Exception $e){
			return response()->json(['status' => 'fail', 'message' => $e->getMessage()]);
		}
	}
	
  public function globalAction(Request $request) {
    $object_type = 'ProjectCarrasco\\'.$request->input('type');
		$object = $object_type::find($request->input('id'));
    
		switch ($request->input('action')) {
		  case 'remove':
        
        switch ($request->input('type')) {
          case 'User':
            
            if ($object->id != \Auth::user()->id) {
              $object->delete();
						} 
            else {                                                                                               
							return response()->json(['status' => 'fail', 'message' => trans('json.cantEliminateCurrentUser')]);
						}
            
				  break;
          case 'Product':
            \DB::table('virtual_routes')
              ->where('route_type', '=', 'p')
              ->where('object_id', '=', $request->input('id'))
              ->delete();
            \DB::table('product_views')
              ->where('product_id', '=', $request->input('id'))
              ->delete();
            \DB::table('product_tags')
              ->where('product_id', '=', $request->input('id'))
              ->delete();
            \DB::table('product_properties')
              ->where('country', get_current_country())
              ->where('language', get_current_language())
              ->where('product_id', '=', $request->input('id'))
              ->delete();
            \DB::table('product_category')
              ->where('product_id', '=', $request->input('id'))
              ->delete();
            $object->delete();
            app('MainService')->updateProductParentFlags();
      		break;
					case 'Category':
						$object->delete();
						app('MainService')->updateCategoriesTreeCache();
					break;
            case 'Brand':
                $object->delete();
                break;
            case 'Store':
                $object->delete();
                break;
					case 'Setup':
						$object->delete();
            \DB::table('translator_languages')->where('locale', strtoupper( $object->country_abre ).'-'.strtolower( $object->language_abre ) )->delete();
					break;
					case 'Translation':
						$object->delete();
					break;
				}
        
      break;
      
      case 'toggle':
        $object->toggle();
				$object->save();
			break;
      
		}
		
    return response()->json(['status' => 'success', 'message' => trans('json.actionDoneSuccessfully')]);
  }
  
}