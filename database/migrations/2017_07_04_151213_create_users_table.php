<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('email')->unique();
			$table->string('password', 60);
			$table->string('gender', 6);
			$table->string('role', 32);
			$table->text('brief_description', 65535)->nullable();
			$table->string('url')->nullable();
			$table->string('city')->nullable();
			$table->string('country')->nullable();
			$table->text('profile_photo_url', 65535)->nullable();
			$table->boolean('active')->default(0);
			$table->string('activate_token')->nullable();
			$table->boolean('newsletter')->nullable();
			$table->string('remember_token', 100)->nullable();
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
		Schema::drop('users');
	}

}
