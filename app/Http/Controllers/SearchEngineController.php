<?php 

  namespace ProjectCarrasco\Http\Controllers;
  use Elasticsearch\Common\Exceptions\Missing404Exception;
  use Illuminate\Http\Request;
  use ProjectCarrasco\TermSearch;
  use Symfony\Component\HttpFoundation\JsonResponse;
  
  class SearchEngineController extends Controller {
    
    public function indexAction() {
		  $client = app('ESClient');
		  
      if ($client) {
        $stats = array();
        $info = array();
        $nodes = array();
        $error = null;
        
        try {
          $info = $client->cluster()->health();
				  $global_stats = $client->indices()->stats(array('index' => settings('app.elasticsearch_index_name')));
				  $stats = $global_stats['_all']['primaries'];
				  $other_info = $client->nodes()->info();
				  $nodes = $other_info['nodes'];
        } 
        catch(Missing404Exception $e) {
          $service = app('MainService');
          $service->updateProductMappingOnElasticsearch(app('ESClient'));
          \Session::flash('notice', trans('flash.notice.search_index_noexist') );
          $global_stats = $client->indices()->stats(array('index' => settings('app.elasticsearch_index_name')));
          $stats = $global_stats['_all']['primaries'];
          $other_info = $client->nodes()->info();
          $nodes = $other_info['nodes'];
			  } 
        catch (\Exception $e){
				  $error = $e->getMessage();
        }
			
        return view('search_engine/index', 
          array(
            'search_engine_available' => true,
				    'error' => $error,
				    'stats' => $stats,
				    'info' => $info,
				    'nodes' => $nodes
			    )
        );
		  } 
      else {
        return view('search_engine/index', 
          [
				    'error' => true
          ]
        );
		  }
	  }
    
    public function updateSearchIndex(Request $request) {
      
      try {
        $service = app('MainService');
        
        if (!$request->input('import_id')) {
          $mapping_response = $service->updateProductMappingOnElasticsearch(app('ESClient'));
				  $service->updateFullElasticsearchIndex();
        } 
        else {
          $service->handleImportSearchEngineUpdate($request->input('import_id'));
        }
        
        if ($request->isXmlHttpRequest()) {
          return new JsonResponse(
            [
              'status' => 'success',
              'message' => trans('json.reindexScheduledSuccess')
				    ]
          );
        } 
        else {
          \Session::flash('success', trans('flash.success.reindex_scheduled') );
				  return redirect(route('search_engine_index'));
        }
        
		  } 
      catch(\Exception $e) {
        throw $e;
        
        if ($request->isXmlHttpRequest()) {
          return new JsonResponse(
            [
              'status' => 'success',
              'message' => trans('json.productsReloadFailed')
				    ]
          );
        } 
        else {
          \Session::flash('error', trans('flash.error.search_update_error') );
				  return redirect(route('search_engine_index'));
        }
		  }
      
    }
	
    public function emptySearchIndex(Request $request) {
		  
      try {
        app('MainService')->updateProductMappingOnElasticsearch(app('ESClient'));
		  } 
      catch(\Exception $e) {
        \Session::flash('error', trans('flash.error.search_empty_error') );
      }
		  
      return redirect(route('search_engine_index'));
    }

    public function latestSearches(Request $request, $page = 1) {
      $searches = TermSearch::paginateForAdmin($page, 10);
      return view('search_engine/latest', 
        array(
          'searches' => $searches
		    )
      );
    }
    
  }
  
