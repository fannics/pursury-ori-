<?php namespace ProjectCarrasco\Http\Requests;

use ProjectCarrasco\Http\Requests\Request;

class BrandUpdateRequest extends Request {

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
            'image' => 'sometimes|image',
            'meta_description' => 'required'

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
            'title.required' => trans('requestvalidation.brands_title.required'),
            'default_sorting.required' => trans('requestvalidation.brands_sorting.required'),
            'url_key.required' => trans('requestvalidation.brands_url.required'),
            'meta_title.required' => trans('requestvalidation.brands_meta_title.required'),
            'meta_description.required' => trans('requestvalidation.brands_meta_title.required')

        ];
    }

}
