<?php namespace ProjectCarrasco;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;

class TranslationCatalog extends Model {

	protected $table = 'translation_catalogs';

	public function translations(){
		return $this->hasMany('ProjectCarrasco\Translation', 'catalog_id', 'id');
	}

    public static function getBaseStrings($catalog_code){
		$fs = new Filesystem();

		$loader = new FileLoader($fs, app_path('../resources/lang/'));

		$strings_array = $loader->load($catalog_code, 'messages');

		return $strings_array;

	}
	
	public function syncronizeWithFiles(){

		$strings = self::getBaseStrings($this->catalog_code);

		foreach($strings as $key=>$string){
			if ($key){
				$found = Translation::where('source', $key)->where('catalog_id', $this->id)->first();

				if (!$found){

					$trans = new Translation();
					$trans->catalog_id = $this->id;
					$trans->source = $key;
					$trans->destination = $string;

					$trans->save();
				}
			}
		}
	}

	public function activate(){

		$translations = Translation::where('catalog_id', $this->id)->get();

		$base_dir = app_path('../resources/lang/');

		if (!is_dir($base_dir.$this->catalog_code)){
			mkdir($base_dir.$this->catalog_code, 0777);
		}

		$base_dir = $base_dir.$this->catalog_code.DIRECTORY_SEPARATOR;

		$handle = fopen($base_dir.'messages.php', 'w');

		fwrite($handle, '<?php'.PHP_EOL);
		fwrite($handle, 'return ['.PHP_EOL);


		foreach($translations as $trans){
			fwrite($handle, "'".$trans->source."' => '".addslashes($trans->destination)."',".PHP_EOL);
		}

		fwrite($handle, '];'.PHP_EOL);

		fclose($handle);

	}

}
