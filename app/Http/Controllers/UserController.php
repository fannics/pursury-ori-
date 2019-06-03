<?php namespace ProjectCarrasco\Http\Controllers;

use ProjectCarrasco\Http\Requests;
use ProjectCarrasco\Http\Controllers\Controller;

use Illuminate\Http\Request;
use ProjectCarrasco\User;

class UserController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request, $page = null)
	{

		if ($request->isXmlHttpRequest()){

			$pagination_fields = $this->getDataTablesInfoForQuery($request->input());

			$users = User::paginateForAdmin($pagination_fields);

			$total_users = User::getUserCount();

			return response()->json($this->convertPaginatorToDataTableInput($users, $total_users));
		}

		return view('user/index');
	}

	public function form(Request $request, $id = null){

		if ($id !== null){
			$user = User::find($id);

			if (!$user){

				\Session::flash('error', trans('flash.error.user_not_found') );

				return redirect(route('admin_users'));
			}

		} else {
			$user = new User();
		}

		return view('user/form', array('user' => $user, 'id' => $id));
	}

	public function formProcess(Requests\UserFormRequest $request, $id = null){

		//handle valid input

		try{
			$user = User::findOrNew($id);

			$user->name = $request->input('user_name');
			$user->email = $request->input('user_email');
			$user->gender = $request->input('user_gender');
			$user->brief_description = $request->input('user_description', null);
			$user->url = $request->input('user_url', null);
			$user->city = $request->input('user_city', null);
			$user->country = $request->input('user_country', null);
			$user->active = $request->input('user_active', false);

            if (!empty(trim($request->input('user_password'))))
            {
             $user->password = \Hash::make($request->input('user_password'));
            }

			$user->newsletter = $request->input('user_newsletter', false);
			$user->role = $request->input('user_role');

			$user->save();

			\Session::flash('success', trans('flash.success.operation_done_successfully'));

			return redirect(route('admin_users'));

		} catch (\Exception $e){

			\Session::flash('error', trans('flash.error.generic_error_operation') );

			return redirect(route('admin_users'));

		}
	}

	public function batchAction(Request $request){

		if ($request->get('action') == 'remove'){

			$ids = $request->input('ids', null);

			$user_id = \Auth::user()->id;

			if (in_array($user_id, $ids)){

				//session message
//				return response()->json(['status' => 'fail', 'message' => 'Usted no puede eliminar su propio usuario']);

				$ids = array_filter($ids, function($value) use($user_id){
					return $value != $user_id;
				});
			}

			if ($ids){
				try{
					foreach($ids as $id){
						\DB::table('users')->delete($id);
					}

					return response()->json(['status' => 'success', 'message' => trans('json.actionDoneSuccessfully')]);

				} catch (\Exception $e){

					\Log::error($e->getMessage());

					return response()->json(['status' => 'fail', 'message' => trans('json.errorRemoveElement')]);
				}
			}

			return response()->json(['status' => 'fail', 'message' => trans('json.mustSelectElements') ]);
		}
		if ($request->get('action') == 'toggle'){

			$ids = $request->input('ids', null);

			$user_id = \Auth::user()->id;

			if (in_array($user_id, $ids)){

				//session message
//				return response()->json(['status' => 'fail', 'message' => 'Usted no puede eliminar su propio usuario']);

				$ids = array_filter($ids, function($value) use($user_id){
					return $value != $user_id;
				});
			}

			if ($ids){
				try{
					foreach($ids as $id){
						$user = User::find($id);

						$user->active = $user->active == true ? false : true;

						$user->save();

					}

					return response()->json(['status' => 'success', 'message' => trans('json.actionDoneSuccessfully')]);

				} catch (\Exception $e){

					\Log::error($e->getMessage());

					return response()->json(['status' => 'fail', 'message' => trans('json.errorTogglingElements') ]);
				}
			}

			return response()->json(['status' => 'fail', 'message' => trans('json.mustSelectElements') ]);
		}

	}
}
