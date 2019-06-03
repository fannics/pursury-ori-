<?php namespace ProjectCarrasco\Console\Commands;

use Illuminate\Console\Command;
use ProjectCarrasco\Product;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class PopularityUpdaterCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'popularity:updater';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Fix to update popularity fields in products';

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

		$products_to_update = Product::where('hits', '>', 0)->orWhere('shop_visits', '>', 0)->count();

		$chunks = ceil($products_to_update / 100);

		for ($i =1; $i <= $chunks; $i++){

			$products_to_update = Product::where('hits', '>', 0)->orWhere('shop_visits', '>', 0)->take(100)->offset((($i -1) * 100))->get();

			foreach($products_to_update as $p){
				$p->popularity = ($p->hits + $p->shop_visits) / 2;
				$p->save();
			}

			unset($products_to_update);

		}

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
