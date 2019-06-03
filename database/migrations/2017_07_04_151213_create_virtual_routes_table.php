<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVirtualRoutesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('virtual_routes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('route_type');
			$table->text('route', 65535);
			$table->integer('object_id')->nullable();
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
		Schema::drop('virtual_routes');
	}

}
