<?php namespace ProjectCarrasco\Http\Requests;

use ProjectCarrasco\Http\Requests\Request;

class ChangePasswordFormRequest extends Request {

	private $size = 6;

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
			'new_password' => 'required|min:'.$this->size,
			'new_password_conf' => 'required|same:new_password'
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
			'new_password.required' => trans('requestvalidation.new_password.required'),
			'new_password.size' => trans('requestvalidation.new_password.size1').' '.$this->size.' '.trans('requestvalidation.new_password.size2'),
			'new_password_conf.required' => trans('requestvalidation.new_password_conf.required'),
			'new_password_conf.same' => trans('requestvalidation.new_password_conf.same')
		];
	}


}
