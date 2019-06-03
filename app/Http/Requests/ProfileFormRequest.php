<?php namespace ProjectCarrasco\Http\Requests;

use ProjectCarrasco\Http\Requests\Request;

class ProfileFormRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return \Auth::getUser() != null;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'username' => 'required',
			'gender' => 'required',
			'url' => 'url'
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
			'username.required' => trans('requestvalidation.username.required'),
			'gender.required' => trans('requestvalidation.gender.required'),
			'url.url' => trans('requestvalidation.url.url')
		];
	}


}
