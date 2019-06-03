<?php namespace ProjectCarrasco\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends BaseController {

	use DispatchesJobs, ValidatesRequests;

	public function convertPaginatorToDataTableInput(LengthAwarePaginator $paginator, $total_count){

		$output_array = [
			'data' => $paginator->items(),
			'recordsTotal' => $total_count,
			'recordsFiltered' => $paginator->total(),
		];

		return $output_array;

	}

	public function getDataTablesInfoForQuery($input)
	{

		$order_obj = $input['order'];
		$sort_fields = [];

		if ($order_obj) {
			foreach ($order_obj as $oo) {
				$sort_fields[] = array(
					'field' => $input['columns'][$oo['column']]['data'],
					'dir' => $oo['dir']
				);
			}
		}

		return array(
			'itemsPerPage' => $input['length'],
			'offset' => $input['start'],
			'sorting' => $sort_fields,
			'filters' => isset($input['search']) && isset($input['search']['value']) ? $input['search']['value'] : null
		);
	}

}
