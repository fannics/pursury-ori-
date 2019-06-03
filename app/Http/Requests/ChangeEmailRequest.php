<?php namespace ProjectCarrasco\Http\Requests;

use ProjectCarrasco\Http\Requests\Request;

class ChangeEmailRequest extends Request {

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
			'new_email' => 'required|email',
			'new_email_conf' => 'required|email|same:new_email'
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
			'new_email.required' => trans('requestvalidation.new_email.required'),
			'new_email.email' => trans('requestvalidation.new_email.email'),
			'new_email_conf.required' => trans('requestvalidation.new_email_conf.required'),
			'new_email_conf.email' => trans('requestvalidation.new_email_conf.email'),
			'new_email_conf.same' => trans('requestvalidation.new_email_conf.same')
		];
	}
}
