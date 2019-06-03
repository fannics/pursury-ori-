<?php namespace ProjectCarrasco\Http\Requests;

use ProjectCarrasco\Http\Requests\Request;

class SettingsFormRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return \Auth::getUser() && \Auth::getUser()->role == 'ROLE_ADMIN';
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$rules = [
			'app_title' => 'required',
			'elasticsearch_host' => 'url',
			'product_file_image_size_width' => 'required|integer|min:1',
			'product_file_image_size_height' => 'required|integer|min:1',
			'thumbnail_size_for_tile_width' => 'required|integer|min:1',
			'thumbnail_size_for_tile_height' => 'required|integer|min:1',
		];

		if ($this->input('image_processor') == 'thumbor'){
			$rules['thumbor_address'] = 'required';
		}

		if ($this->input('elasticsearch_host')){
			$rules['elasticsearch_index_name'] = 'required';
		}

		return $rules;
	}

	public function messages(){
		return array(
			'app_title.required' => trans('requestvalidation.app_title.required'),

			'elasticsearch_host.url' => trans('requestvalidation.elasticsearch_host.url'),
			'elasticsearch_index_name.required' => trans('requestvalidation.elasticsearch_index_name.required'),

			'product_file_image_size_width.required' => trans('requestvalidation.product_file_image_size_width.required'),
			'product_file_image_size_width.integer' => trans('requestvalidation.product_file_image_size_width.integer'),
			'product_file_image_size_width.min' => trans('requestvalidation.product_file_image_size_width.min'),

			'product_file_image_size_height.required' => trans('requestvalidation.product_file_image_size_height.required'),
			'product_file_image_size_height.integer' => trans('requestvalidation.product_file_image_size_height.integer'),
			'product_file_image_size_height.min' => trans('requestvalidation.product_file_image_size_height.min'),

			'thumbnail_size_for_tile_width.required' => trans('requestvalidation.thumbnail_size_for_tile_width.required'),
			'thumbnail_size_for_tile_width.integer' => trans('requestvalidation.thumbnail_size_for_tile_width.integer'),
			'thumbnail_size_for_tile_width.min' => trans('requestvalidation.thumbnail_size_for_tile_width.min'),

			'thumbnail_size_for_tile_height.required' => trans('requestvalidation.thumbnail_size_for_tile_height.required'),
			'thumbnail_size_for_tile_height.integer' => trans('requestvalidation.thumbnail_size_for_tile_height.integer'),
			'thumbnail_size_for_tile_height.min' => trans('requestvalidation.thumbnail_size_for_tile_height.min')

		);
	}

}
