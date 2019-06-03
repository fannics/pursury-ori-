<?php namespace ProjectCarrasco\Console\Commands;

use Illuminate\Console\Command;
use ProjectCarrasco\Product;
use ProjectCarrasco\VirtualRouting;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RouteTestCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'export:routes';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import product and category routes to the database';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{

//		
//
//		app('MainService')->storeProductRoutes();
//
//		app('MainService')->storeCategoryRoutes();

	}

}
