<?php namespace ProjectCarrasco;

use Illuminate\Database\Eloquent\Model;
use ProjectCarrasco\Paginator\AppPaginator;

class TermSearch extends Model {

    public static function getSearchesCount(){
        return \DB::table('term_searches')->count();
    }

    public static function getPaginatedSearches($page, $itemsPerPage, $sort_field = 'term_searches.created_at', $sort_direction = 'DESC'){
        return \DB::table('term_searches')
            ->select(\DB::raw('term_searches.created_at, term_searches.results_found, term_searches.used_term, users.email, users.name'))
            ->take($itemsPerPage, ($page - 1) * $itemsPerPage)
            ->leftJoin('users', 'users.id', '=', 'term_searches.user_id')
            ->skip(($page - 1) * $itemsPerPage)
            ->orderBy($sort_field, $sort_direction)
            ->get();
    }

	public static function paginateForAdmin($page, $itemsPerPage){

        return new AppPaginator(
            self::getPaginatedSearches($page, $itemsPerPage),
            self::getSearchesCount(),
            $itemsPerPage,
            $page
        );
    }

}
