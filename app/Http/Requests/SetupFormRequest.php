<?php namespace ProjectCarrasco\Http\Requests;

use ProjectCarrasco\Http\Requests\Request;

class SetupFormRequest extends Request {

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
			'setup_country' => 'required',
      'setup_country_abre' => 'required',
		  'setup_language' => 'required',
		  'setup_language_abre' => 'required',
		  'setup_currency' => 'required',
		  'setup_currency_symbol' => 'required',
		  'setup_before_after' => 'required',
		  'setup_currency_decimal' => 'required',
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
			'setup_country.required' => trans('requestvalidation.setup_country.required'),
			'setup_country_abre.required' => trans('requestvalidation.setup_country_abre.required'),
			'setup_language.required' => trans('requestvalidation.setup_language.required'),
			'setup_language_abre.required' => trans('requestvalidation.setup_language_abre.required'),
      'setup_currency.required' => trans('requestvalidation.setup_currency.required'),
      'setup_currency_symbol.required' => trans('requestvalidation.setup_currency_symbol.required'),
      'setup_before_after.required' => trans('requestvalidation.setup_before_after.required'),
      'setup_currency_decimal.required' => trans('requestvalidation.setup_currency_decimal.required')
		];
	}


}
