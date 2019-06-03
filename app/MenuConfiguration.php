<?php namespace ProjectCarrasco;

use Illuminate\Database\Eloquent\Model;

class MenuConfiguration extends Model {

    public function category(){
        return $this->hasOne('ProjectCarrasco\Category', 'id', 'category_id');
    }

}
