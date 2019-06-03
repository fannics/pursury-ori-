<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTermSearchesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('term_searches', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->string('used_term');
			$table->integer('results_found')->nullable();
			$table->integer('user_id')->unsigned()->nullable()->index('term_searches_user_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('term_searches');
	}

}
