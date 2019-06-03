<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSetupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('setups', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('country', 100);
			$table->string('country_abre', 2);
			$table->string('language', 100);
			$table->boolean('default_language');
			$table->string('language_abre', 2);
			$table->string('currency', 3);
			$table->string('currency_symbol', 10);
			$table->boolean('before_after');
			$table->string('currency_decimal', 10);
			$table->timestamps();
			$table->boolean('default_setup');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('setups');
	}

}
