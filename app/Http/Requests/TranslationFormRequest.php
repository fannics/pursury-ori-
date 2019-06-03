<?php namespace ProjectCarrasco\Http\Requests;

use ProjectCarrasco\Http\Requests\Request;

class TranslationFormRequest extends Request {

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
			'translation_group' => 'required',
      'translation_item' => 'required',
		  'translation_text' => 'required',
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
			'translation_group.required' => trans('requestvalidation.translation_group.required'),
			'translation_item.required' => trans('requestvalidation.translation_item.required'),
			'translation_text.required' => trans('requestvalidation.translation_text.required')
		];
	}


}
