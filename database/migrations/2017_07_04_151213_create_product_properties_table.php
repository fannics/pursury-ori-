<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductPropertiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_properties', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->string('name')->index('name');
			$table->string('value')->nullable()->index('value');
			$table->integer('product_id')->unsigned()->nullable()->index('product_properties_product_id_foreign');
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
		Schema::drop('product_properties');
	}

}
