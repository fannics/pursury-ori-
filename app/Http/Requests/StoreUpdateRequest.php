<?php namespace ProjectCarrasco\Http\Requests;

use ProjectCarrasco\Http\Requests\Request;

class StoreUpdateRequest extends Request {

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
            'name' => 'required',
            'url_key' => 'required',
            'meta_title' => 'required',
            'meta_description' => 'required',
            'logo' => 'sometimes|image',
            'logo_thumb' => 'sometimes|image',

        ];
	}

    public function messages()
    {
        return [
            'name.required' => trans('requestvalidation.stores_name.required'),
            'url_key.required' => trans('requestvalidation.brands_url.required'),
            'meta_title.required' => trans('requestvalidation.brands_meta_title.required'),
            'meta_description.required' => trans('requestvalidation.brands_meta_title.required'),

        ];
    }

}
