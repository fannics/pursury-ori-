<?php namespace ProjectCarrasco\Providers;

use Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;
use ProjectCarrasco\Services\MainService;

class MainServiceProvider extends ServiceProvider {

	private $service;

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{

	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('MainService', function($app){
			return new MainService();
		});

		$this->app->bind('ESClient', function($app){

			try{

				$params = [
					'hosts' => [
						settings('app.elasticsearch_host')
					],
					'retries' => 5,
				];

				return ClientBuilder::fromConfig($params);

			} catch (\Exception $e){

				return null;

			}
		});
	}

}
