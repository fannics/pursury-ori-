<?php namespace ProjectCarrasco\Console\Commands;

use Illuminate\Console\Command;
use ProjectCarrasco\ProductProperty;
use ProjectCarrasco\ProductTag;
use ProjectCarrasco\Tags;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ChangeToTagsCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'ent:change-to-tags';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Change from the product properties schema to a tag based schema';

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

		$self = $this;

		\DB::table('product_properties')->chunk(200, function($props) use ($self){

			foreach($props as $prop){

				//Look if the tag exists
				$tag = Tags::where('tag_name', $prop->name)
					->where('tag_value', $prop->value)
					->first();

				if (!$tag){
					$tag = new Tags();
          $sessionCountry = isset($_COOKIE['sessionCountry']) ? $_COOKIE['sessionCountry'] : '';
          $sessionLanguage = isset($_COOKIE['sessionLanguage']) ? $_COOKIE['sessionLanguage'] : '';
          $currentLocale = get_current_locate($sessionCountry,$sessionLanguage);
          $currentLocale = str_replace('/','',$currentLocale);
          $pieces = explode("-", $currentLocale);
          $tag->country = strtolower($pieces[0]);
          $tag->language = strtolower($pieces[1]);
					$tag->tag_name = $prop->name;
					$tag->tag_value = $prop->value;
					$tag->save();
				}

				//check if the product exists in the product_tags_table
				$product_tag = ProductTag::where('product_id', $prop->product_id)
					->where('tag_id', $tag->id)
					->first();

				if (!$product_tag){

					$product_tag = new ProductTag();
					$product_tag->tag_id = $tag->id;
					$product_tag->product_id = $prop->product_id;

					$product_tag->save();

				}

				unset($tag, $product_tag);

			}

		});

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
		return [];
	}

}
