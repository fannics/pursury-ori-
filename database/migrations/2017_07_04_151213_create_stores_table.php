<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStoresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stores', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name')->nullable();
			$table->string('url_key');
			$table->boolean('is_visible')->nullable()->default(1);
			$table->string('meta_title')->nullable();
			$table->text('meta_description')->nullable();
			$table->boolean('meta_noindex')->nullable()->default(0);
			$table->string('logo')->nullable();
			$table->string('logo_thumb')->nullable();
			$table->string('country', 4)->nullable();
			$table->string('language', 4)->nullable();
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
		Schema::drop('stores');
	}

}
