<?php namespace ProjectCarrasco\Http\Requests;

use ProjectCarrasco\Http\Requests\Request;

class RegisterFormRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
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
			'email' => 'required|email',
			'password' => 'required|min:6',
			'password_confirmation' => 'required|same:password',
			'gender' => 'required',
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
			'name.required' => trans('requestvalidation.name.required'),
			'email.required' => trans('requestvalidation.email.required'),
			'email.email' => trans('requestvalidation.email.email'),
			'password.required' => trans('requestvalidation.password.required'),
			'password.min' => trans('requestvalidation.password.min'),
			'password_confirmation.required' => trans('requestvalidation.password_confirmation.required'),
			'password_confirmation.same' => trans('requestvalidation.password_confirmation.same'),
			'gender.required' => trans('requestvalidation.gender.required')
		];
	}


}
