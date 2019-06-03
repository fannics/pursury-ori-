<?php namespace ProjectCarrasco\Http\Controllers;

use ProjectCarrasco\Http\Requests;
use ProjectCarrasco\Http\Controllers\Controller;

use Illuminate\Http\Request;
use ProjectCarrasco\Product;
use ProjectCarrasco\Wishlist;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProfileController extends Controller {

	public function changePassword(Request $request){

		return view('main/changePassword');

	}

	public function handleChangePassword(Requests\ChangePasswordFormRequest $request){

		$user = \Auth::user();

		$user->password = \Hash::make($request->input('new_password'));

		$user->save();

		\Auth::login($user);

		\Session::flash('success', trans('flash.success.password_changed') );

		return redirect(route('profile_edit'));
	}

	public function changeEmail(Request $request){
		return view('main/changeEmail');
	}

	public function handleChangeEmail(Requests\ChangeEmailRequest $request){

		$user = \Auth::user();

		$previous_email = $user->email;

		$user->email = $request->input('new_email');

		//notify by email to the two accounts
		\Mail::send('emails/email_changed', array('email_address' => $user->email), function($message) use ($previous_email){

			$message->to($previous_email)->subject(\Config::get('app')['app_title'].' - '. trans('emails.email_change_notification'));

		});

		\Mail::send('emails/email_changed', array('email_address' => $user->email), function($message) use ($user){

			$message->to($user->email)->subject(\Config::get('app')['app_title'].' - '. trans('emails.email_change_notification'));

		});

		$user->save();

		\Session::flash('success', trans('flash.email_changed_successfully') );

		return redirect(route('profile_edit'));
	}

	public function wishList(Request $request){
		return view('main/wishlist');
	}

	public function profile(Request $request){

		$user = \Auth::user();

		$products_on_wishlist = Wishlist::getProductsOnUserWishlist($user->id);

		return view('main/profile', array('user' => $user, 'wishlist' => $products_on_wishlist));
	}

	public function profileEdit(Request $request){

		$user = \Auth::user();

		return view('main/profile_edit', array('user' => $user));
	}

	public function handleProfileEdit(Requests\ProfileFormRequest $request){

		$user = \Auth::user();

		$user->name = $request->input('username');
		$user->gender = $request->input('gender');

		$user->city = $request->input('city');
		$user->country = $request->input('country');

		$user->brief_description = $request->input('brief_description');
		$user->url = $request->input('url');

		$user->save();

		\Session::flash('success', trans('flash.success.data_modified_successfully') );

		return redirect(route('profile'));
	}

	public function wishlistToggle(Request $request){

		if ($request->request->has('p')){

			$user_id = \Auth::user()->id;

			$product = Product::find($request->request->get('p'));

			if ($product){
				//find out if the user has the product on the wishlist
				$wishlist_item = Wishlist::findByUserAndProductId($user_id, $request->request->get('p'));

				if ($wishlist_item){

					Wishlist::query()->where('id', $wishlist_item->id)->delete();

					return new JsonResponse(array(
						'status' => 'success',
						'message' => 'removed'
					));
				} else {
					//add the product to the wishlist
					$wishlist_item = new Wishlist();
					$wishlist_item->user_id = $user_id;
					$wishlist_item->product_id = $request->request->get('p');
					$wishlist_item->save();

					return new JsonResponse(array(
						'status' => 'success',
						'message' => 'added'
					));
				}
			} else {
				return new JsonResponse(array(
					'status' => 'fail',
					'message' => trans('json.invalidProduct')
				));
			}
		} else {
			return new JsonResponse(array(
				'status' => 'fail',
				'message' => trans('json.invalidRequest')
			));
		}
	}


	public function changeNotifications(Request $request){

		return view('main/changeNotifications', array(
			'user' => \Auth::user()
		));

	}

	public function handleChangeNotifications(Requests\NotificationsChangeFormRequest $request){

		$user = \Auth::user();

		$user->newsletter = $request->input('newsletter') == 1 ? true : false;

		$user->save();

		\Session::flash('success', trans('flash.success.data_modified_successfully'));

		return redirect(route('profile_edit'));

	}



}
