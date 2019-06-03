<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateColorCodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('color_codes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('color_name')->index('color_name');
			$table->string('color_code')->index('color_code');
			$table->timestamps();
			$table->boolean('full')->nullable();
			$table->string('country', 2)->nullable();
			$table->string('language', 2)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('color_codes');
	}

}
