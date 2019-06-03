<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('product_id');
			$table->string('title');
			$table->text('short_description', 65535);
			$table->text('description', 65535);
			$table->string('url_key');
			$table->boolean('is_visible')->index('is_visible_index');
			$table->text('image', 65535)->nullable();
			$table->text('thumbnail', 65535)->nullable();
			$table->float('price')->nullable()->index('price');
			$table->text('destination_url', 65535);
			$table->integer('hits')->default(0);
			$table->integer('shop_visits')->default(0);
			$table->float('popularity')->default(0.00)->index('popularity_index');
			$table->string('brand')->nullable();
			$table->float('previous_price')->nullable();
			$table->string('meta_title');
			$table->text('meta_description', 65535);
			$table->boolean('meta_index');
			$table->timestamps();
			$table->integer('category_sort')->nullable();
			$table->string('country', 2)->nullable();
			$table->string('language', 2)->nullable();
			$table->integer('parent_id')->unsigned()->nullable();
			$table->string('store', 81)->nullable();
			$table->string('image_alt', 81)->nullable();
			$table->float('shipping_cost')->nullable();
			$table->boolean('winner')->nullable();
			$table->boolean('stock')->nullable();
			$table->string('parent_filters')->nullable();
			$table->boolean('is_parent')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('products');
	}

}
