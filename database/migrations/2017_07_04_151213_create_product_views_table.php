<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductViewsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_views', function(Blueprint $table)
		{
			$table->increments('id');
			$table->dateTime('date_of_view')->default('0000-00-00 00:00:00');
			$table->integer('product_id')->unsigned()->nullable()->index('product_views_product_id_foreign');
			$table->string('ip_address')->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('product_views');
	}

}
