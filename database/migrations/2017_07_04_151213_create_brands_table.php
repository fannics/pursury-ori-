<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBrandsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('brands', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->text('name')->nullable();
			$table->text('short_description')->nullable();
			$table->text('title')->nullable();
			$table->text('url_key')->nullable();
			$table->boolean('is_visible')->nullable()->default(1);
			$table->text('meta_title')->nullable();
			$table->text('meta_description')->nullable();
			$table->boolean('meta_noindex')->nullable()->default(0);
			$table->string('default_sorting')->nullable();
			$table->string('image')->nullable();
			$table->string('country', 4)->nullable();
			$table->string('language', 4)->nullable();
			$table->text('description')->nullable();
			$table->softDeletes();
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
		Schema::drop('brands');
	}

}
