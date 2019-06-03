<?php namespace ProjectCarrasco\Http\Requests;

use ProjectCarrasco\Http\Requests\Request;

class CategoryFormRequest extends Request {

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
			'title' => 'required',
			'default_sorting' => 'required',
			'url_key' => 'required',
			'meta_title' => 'required',
            'img' => 'sometimes|image',
            'img_thumbnail' => 'sometimes|image'
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
			'title.required' => trans('requestvalidation.category_title.required'),
			'default_sorting.required' => trans('requestvalidation.category_sorting.required'),
			'url.required' => trans('requestvalidation.category_url.required'),
			'meta_title.required' => trans('requestvalidation.category_meta_title.required')
		];
	}


}
