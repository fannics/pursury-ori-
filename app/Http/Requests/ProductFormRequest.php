<?php namespace ProjectCarrasco\Http\Requests;

use ProjectCarrasco\Http\Requests\Request;

class ProductFormRequest extends Request {

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
		return [
			'product_title' => 'required|min:4',
			'product_id' => 'required',
			'product_category' => 'required',
			'product_url' => 'required',
			'product_destination_url' => 'required|url',
			'product_price' => 'required|numeric|min:0',
			'product_previous_price' => 'numeric|min:0',
			'product_meta_title' => 'required',
			'product_meta_description' => 'required',
		];
	}

	/**
	 * Set custom messages for validator errors.
	 *
	 * @return array
	 */
	public function messages()
	{
		return [
			'product_title.required' => trans('requestvalidation.product_title.required'),
			'product_title.min' => trans('requestvalidation.product_title.min'),
			'product_id.required' => trans('requestvalidation.product_id.required'),
			'product_category.required' => trans('requestvalidation.product_category.required'),
			'product_url.required' => trans('requestvalidation.product_url.required'),
			'product_destination_url.required' => trans('requestvalidation.product_destination_url.required'),
			'product_destination_url.url' => trans('requestvalidation.product_destination_url.url'),
			'product_previous_price.numeric' => trans('requestvalidation.product_previous_price.numeric'),
			'product_previous_price.min' => trans('requestvalidation.product_previous_price.min'),
			'product_price.required' => trans('requestvalidation.product_price.required'),
			'product_price.numeric' => trans('requestvalidation.product_price.numeric'),
			'product_price.min' => trans('requestvalidation.product_price.min'),
			'product_meta_title.required' => trans('requestvalidation.product_meta_title.required'),
			'product_meta_description.required' => trans('requestvalidation.product_meta_description.required')
		];
	}


}
