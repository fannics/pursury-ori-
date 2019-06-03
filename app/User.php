<?php namespace ProjectCarrasco;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use ProjectCarrasco\Paginator\AppPaginator;

/**
 * ProjectCarrasco\User
 *
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password', 'gender', 'role', 'active', 'activate_token', 'newsletter'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];


	public static function findOneByActivationToken($token){

		return self::query()->where('activate_token', $token)->firstOrFail();

	}

	public static function getPaginatedUsers($paginationFields){

		$query = \DB::table('users')
			->take($paginationFields['itemsPerPage'])
			->skip($paginationFields['offset']);

		foreach($paginationFields['sorting'] as $sort_field){

			switch($sort_field['field']){
				case 'name':
					$query->orderBy('name', $sort_field['dir']);
					break;
				case 'email':
					$query->orderBy('email', $sort_field['dir']);
					break;
				case 'created_at':
					$query->orderBy('created_at', $sort_field['dir']);
					break;
				case 'active':
					$query->orderBy('active', $sort_field['dir']);
					break;
				case 'newsletter':
					$query->orderBy('newsletter', $sort_field['dir']);
					break;
			}

		}

		if (isset($paginationFields['filters']) && count($paginationFields['filters']) > 0){
			$query->where(function($query) use ($paginationFields){
				$query->orWhere('name', 'like', '%'.$paginationFields['filters'].'%')
					->orWhere('email', 'like', '%'.$paginationFields['filters'].'%');
			});
		}

		$res = $query->get(['name', 'email', 'gender', 'active', 'newsletter', 'created_at', 'id']);

		$results = [];

		foreach($res as $user){
			$results[] = [
				'id' => $user->id,
				'name' => $user->name,
				'email' => $user->email,
				'gender' => !$user->gender ? 'No Def.' : ($user->gender == 'male' ? 'Masc.' : 'Fem.'),
				'active' => $user->active,
				'newsletter' => $user->newsletter,
				'created_at' => \DateTime::createFromFormat('Y-m-d H:i:s', $user->created_at)->format('d/m/Y H:i a')
			];
		}

		return $results;

	}

	public static function getUserCount($paginationFields = null){
		
		$query = self::query();


		if (isset($paginationFields['filters']) && count($paginationFields['filters']) > 0){
			$query->where(function($query) use ($paginationFields){
				$query->orWhere('name', 'like', '%'.$paginationFields['filters'].'%')
					->orWhere('email', 'like', '%'.$paginationFields['filters'].'%');
			});
		}

		return $query->count();
	}

	public static function paginateForAdmin($paginationFields){
		return new AppPaginator(
			self::getPaginatedUsers($paginationFields),
			self::getUserCount($paginationFields),
			$paginationFields['itemsPerPage'],
			ceil($paginationFields['offset'] / $paginationFields['itemsPerPage']) + 1
		);
	}

	public function toggle(){
		$this->active = $this->active == true ? false : true;
	}
}
