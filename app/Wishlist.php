<?php namespace ProjectCarrasco;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model {

	public function user(){
        return $this->hasOne('ProjectCarrasco\User', 'id', 'user_id');
    }

	public function product(){
        return $this->hasOne('ProjectCarrasco\Product', 'id', 'product_id');
    }


    public static function findByUserAndProductId($user_id, $product_id){
        return self::query()->where('user_id', $user_id)->where('product_id', $product_id)->first();
    }

    public static function getProductsOnUserWishlist($user_id){

        return self::with(['product' => function($query){
                $query->where('is_visible', true);
            }])
            ->where('user_id', $user_id)
            ->get();

    }
}
