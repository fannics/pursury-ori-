<?php namespace ProjectCarrasco\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use ProjectCarrasco\Console\Commands\ChangeToTagsCommand;
use ProjectCarrasco\Console\Commands\CheckRoutesCommand;
use ProjectCarrasco\Console\Commands\CreateAdminCommand;
use ProjectCarrasco\Console\Commands\PopularityUpdaterCommand;
use ProjectCarrasco\Console\Commands\RouteTestCommand;

class Kernel extends ConsoleKernel {

	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'ProjectCarrasco\Console\Commands\Inspire',
        CreateAdminCommand::class,
		CheckRoutesCommand::class,
		RouteTestCommand::class,
		PopularityUpdaterCommand::class,
		ChangeToTagsCommand::class
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule->command('inspire')
				 ->hourly();
	}

}
