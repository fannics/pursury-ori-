<?php namespace ProjectCarrasco\Console\Commands;

use Illuminate\Console\Command;
use ProjectCarrasco\Product;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CheckRoutesCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'ent:check-routes';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Checks that the products are displaying correctly one by one';

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

		$products_to_check = Product::with('categories')->visible()->count();

		$chunks = ceil($products_to_check / 10);

		$amount = 0;

		$base_url = \Config::get('app')['url'];

		if (ends_with($base_url,'/')){
			$base_url = substr($base_url, 0, strlen($base_url) - 1);
		}

		try{

			$mh = curl_multi_init();

			for ($i =1; $i <= $chunks; $i++){

				$products_to_check = Product::with('categories')->visible()->take(100)->offset((($i -1) * 10))->get();

				$handles = [];

				foreach($products_to_check as $product){

					$url = $base_url.$product->url_key;

					$ch = curl_init($url);

					curl_setopt($ch, CURLOPT_NOBODY, true);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_HEADER, true);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

					curl_multi_add_handle($mh, $ch);

				}

				$runningHandles = null;

				do {
					curl_multi_exec($mh, $runningHandles);
					curl_multi_select($mh);
				} while ($runningHandles > 0);

				$this->output->writeln('done');

				unset($products_to_check);

			}

			curl_multi_close($mh);

		} catch (\Exception $e){

			return array('status' => 'fail', 'message' => $e->getMessage());

		}




	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [

		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [

		];
	}

}
