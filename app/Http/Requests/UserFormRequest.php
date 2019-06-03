<?php namespace ProjectCarrasco\Http\Requests;

use ProjectCarrasco\Http\Requests\Request;

class UserFormRequest extends Request {

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
			'user_name' => 'required',
			'user_gender' => 'required|in:male,female',
			'user_url' => 'url'
		];

		if ($this->input('user_password')){
			$rules['user_password_confirm'] = 'required|same:user_password';
		}

		if ($this->input('id')){
			//editing case
			$rules['user_email'] = 'required|email|unique:users,email,'.$this->input('id');
		} else {
			//creating case
			$rules['user_email'] = 'required|email|unique:users,email';
		}

		return $rules;
	}

}
