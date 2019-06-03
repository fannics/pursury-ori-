<?php namespace ProjectCarrasco;

use Illuminate\Database\Eloquent\Model;
use ProjectCarrasco\Paginator\AppPaginator;

class Import extends Model {

    public static function paginatedItemsForAdmin($page, $items_per_page){

        $items = \DB::table('imports')
            ->select(\DB::raw('users.email, users.name, imports.filename, imports.created_at, imports.type'))
            ->join('users', 'imports.user_id', '=', 'users.id')
            ->take($items_per_page)
            ->orderBy('imports.created_at', 'DESC')
            ->skip($items_per_page * ($page - 1));

        return $items->get();
    }

    public static function importsCount(){

        $count = \DB::table('imports')->count();

        return $count;
    }

	public static function paginatedForAdmin($page, $items_per_page){

        return new AppPaginator(
            self::paginatedItemsForAdmin($page, $items_per_page),
            self::importsCount(),
            $items_per_page,
            $page
        );

    }

}
