<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTranslatorTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('translator_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('locale', 6);
			$table->string('namespace', 100)->default('*');
			$table->string('group', 100);
			$table->string('item', 100);
			$table->text('text', 65535);
			$table->boolean('unstable')->default(0);
			$table->boolean('locked')->default(0);
			$table->timestamps();
			$table->unique(['locale','namespace','group','item']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('translator_translations');
	}

}
