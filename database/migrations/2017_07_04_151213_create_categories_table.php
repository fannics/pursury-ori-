<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('categories', function(Blueprint $table)
		{
			$table->integer('id')->unsigned()->primary();
			$table->string('categories');
			$table->text('short_description', 65535);
			$table->string('title');
			$table->string('url_key');
			$table->boolean('is_visible');
			$table->string('filters');
			$table->string('default_sorting');
			$table->string('meta_title');
			$table->text('meta_description', 65535);
			$table->boolean('meta_no_index');
			$table->integer('parent_id')->unsigned()->nullable()->index('categories_parent_id_foreign');
			$table->timestamps();
			$table->float('popularity')->nullable();
			$table->integer('lft')->default(0)->index('lft');
			$table->integer('rgt')->default(0)->index('rgt');
			$table->integer('position')->nullable();
			$table->string('country', 2)->nullable();
			$table->string('language', 2)->nullable();
			$table->string('reference')->nullable();
			$table->softDeletes();
			$table->string('img')->nullable();
			$table->string('img_thumbnail')->nullable();
			$table->string('img_alt')->nullable();
			$table->text('description')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('categories');
	}

}
