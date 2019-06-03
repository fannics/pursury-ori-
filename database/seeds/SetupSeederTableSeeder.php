<?php

use Illuminate\Database\Seeder;
use ProjectCarrasco\Setup;
use ProjectCarrasco\Category;
use ProjectCarrasco\Product;
use ProjectCarrasco\Brand;
use ProjectCarrasco\Store;

class SetupSeederTableSeeder extends Seeder {

	public function run()
    {
        if (!file_exists(storage_path('seeds/portal-seed.txt')))
        {
            file_put_contents(storage_path('seeds/portal-seed.txt'),'first_time^false');

            app('excel')->load(storage_path('seeds/Settings.xlsx'),function($reader){
                $reader->each(function($sheet) {
                    $methodName = camel_case(str_replace(' ','',$sheet->getTitle()));
                    // Loop through all rows
                    $this->$methodName($sheet);
                });
            });

            \DB::unprepared(file_get_contents(storage_path('seeds/translator_languages.sql')));
            \DB::unprepared(file_get_contents(storage_path('seeds/translator_translations.sql')));

        }


	}

	private function masterSetup($sheet)
    {
        $sheet->each(function($row) {

            $setup =  Setup::where('country',$row['country'])
                ->where('language',$row['language'])
                ->first();

            if (is_null($setup))
            {
                Setup::create([
                    'country' => $row['country'],
                    'country_abre' => $row['country_abre'],
                    'language' => $row['language'],
                    'default_language' => $row['default_language'] == 'yes' ? 1 : 0,
                    'language_abre' => $row['language_abre'],
                    'currency' => $row['currency'],
                    'currency_symbol' => $row['currency_symbol'],
                    'before_after' => $row['symbol_position'] == 'after' ? 1 : 0,
                    'currency_decimal' => $row['currency_decimal'],
                    'default_setup' => $row['default_setup'] == 'yes' ? 1 : 0
                ]);
            }

        });
    }

    private function categories($sheet)
    {
        $sheet->each(function($row) {

            $category = Category::find($row['category_id']);

            if (is_null($category))
            {
                Category::create( [
                    'id' => $row['category_id'],
                    'categories' => utf8_encode($row['categories']),
                    'short_description' => utf8_encode($row['category_short_description']),
                    'title' => utf8_encode($row['category_title']),
                    'url_key' => addslashes($row['category_url_key']),
                    'is_visible' => strtolower($row['category_is_visible']) == 'yes' ? 1 : 0,
                    'meta_title' => utf8_encode($row['category_meta_title']),
                    'meta_description' => utf8_encode($row['category_meta_description']),
                    'meta_no_index' => strtolower($row['category_meta_noindex']) == 'yes' ? 1 : 0,
                    'filters' => utf8_encode($row['category_filters']),
                    'default_sorting' => utf8_encode($row['category_default_sorting']),
                    'parent_id' => $row['category_parent'] !== null ? $row['category_parent'] : null,
                    'country' => $row['category_country'],
                    'language' => $row['category_language'],
                    'reference' => $row['category_reference'] !== '' ? $row['category_reference'] : null,
                    'img' =>  isset($row['category_img']) ? substr($row['category_img'], strrpos($row['category_img'], '/') + 1) : null ,
                    'img_thumbnail' => isset($row['category_img_thumbnail']) ? substr($row['category_img_thumbnail'], strrpos($row['category_img_thumbnail'], '/') + 1) : null,
                    'img_alt' => isset($row['category_img_alt']) ? $row['category_img_alt'] : null,
                    'description' => $row['category_description'] !== '' ? $row['category_description'] : null,

                ]);

                $service = app('MainService');

                $service->updateCategoryTree($row['category_language'],$row['category_country']);
                $service->updateCategoriesTreeCache($row['category_language'],$row['category_country']);
                $service->storeCategoryRoutes($row['category_language'],$row['category_country']);
            }

        });


    }

    private function products ($sheet)
    {
        $sheet->each(function($row) {

            $product = Product::where('product_id', $row['product_id'])->first();

            if (is_null($product))
            {
              $product =   Product::create([
                'title' => utf8_encode($row['product_title']),
                'product_id' => $row['product_id'],
                'short_description' => utf8_encode($row['product_short_description']),
                'description' => utf8_encode($row['product_description']),
                'url_key' => lintUrl($row['product_url_key']),
                'is_visible' => strtolower($row['product_is_visible']) == 'yes' ? 1 : 0,
                'meta_title' => utf8_encode($row['product_meta_title']),
                'image' => $row['product_image'],
                'thumbnail' => $row['product_image_thumbnail'],
                'meta_description' => utf8_encode($row['product_meta_description']),
                'meta_index' => strtolower($row['product_meta_noindex']) == 'yes' ? 1 : 0,
                'price' => $row['product_price'],
                'destination_url' => $row['product_ext_link'],
                'previous_price' => $row['product_old_price'] ? $row['product_old_price'] : null,
                'brand' => utf8_encode($row['product_brand']) ? utf8_encode($row['product_brand']) : null,
                'category_sort' => isset($row['product_position']) && $row['product_position'] ? $row['product_position'] : null,
                'country' => $row['product_country'],
                'language' => $row['product_language'],
                'parent_id' => isset($row['product_parent'])  && $row['product_parent'] ? $row['product_parent'] : null,
                'store' => isset($row['product_store']) && $row['product_store'] ? $row['product_store'] : null,
                'image_alt' => isset($row['product_image_alt']) && $row['product_image_alt'] ? $row['product_image_alt'] : null,
                'shipping_cost' => isset($row['product_shipping_cost']) && $row['product_shipping_cost'] ? $row['product_shipping_cost'] : null,
                'winner' => isset($row['product_winner']) && (isset($row['product_winner']) &&  strtolower($row['product_winner']) == 'yes') ? 1 : 0,
                'stock' => isset($row['product_stock']) && strtolower($row['product_stock']) ==  1 ? 1 : 0,
                'is_parent' =>  isset($row['product_is_parent']) && strtolower($row['product_is_parent']) == 'yes' ? 1 : 0,
                'parent_filters' => isset($row['product_parent_filter']) && $row['product_parent_filter'] ? $row['product_parent_filter'] : null
                ]);

                $this->updateCategories($product->id, $row['product_category']);
                app('MainService')->storeProductRoutes($row['product_language'],$row['product_country']);
            }

        });


 
    }

    private function brand($sheet)
    {
        $sheet->each(function($row) {

            $brand = Brand::find( $row['brand_id']);

            if (is_null($brand))
            {
                   Brand::create([
                   'name' => $row['brand_name'],
                   'short_description' => utf8_encode($row['brand_short_description']),
                   'title' => utf8_encode($row['brand_title']),
                   'url_key' => addslashes($row['brand_url_key']),
                   'is_visible' => strtolower($row['brand_is_visible']) == 'yes' ? 1 : 0,
                   'meta_title' => utf8_encode($row['brand_meta_title']),
                   'meta_description' => utf8_encode($row['brand_meta_description']),
                   'meta_noindex' => strtolower($row['brand_meta_noindex']) == 'yes' ? 1 : 0,
                   'default_sorting' => utf8_encode($row['brand_default_sorting']),
                   'image' =>  isset($row['brand_image']) ? substr($row['brand_image'], strrpos($row['brand_image'], '/') + 1) : null ,
                   'description' => $row['brand_description'] !== null ? $row['brand_description'] : null,
                   'language' => $row['brand_language'],
                    'country' => $row['brand_country']
                   ]);

                app('MainService')->updateBrandsVirtualRoutes($row['brand_language'],$row['brand_country']);

            }

        });

    }

    private function stores($sheet)
    {
        $sheet->each(function($row) {

            $store = Store::find( $row['store_id']);

            if (is_null($store))
            {
                Store::create([
                'id' => $row['store_id'],
                'url_key' => addslashes($row['store_url_key']),
                'is_visible' => strtolower($row['store_is_visible']) == 'yes' ? 1 : 0,
                'meta_title' => utf8_encode($row['store_meta_title']),
                'meta_description' => utf8_encode($row['store_meta_description']),
                'meta_noindex' => strtolower($row['store_meta_noindex']) == 'yes' ? 1 : 0,
                'logo' =>  isset($row['store_logo']) ? substr($row['store_logo'], strrpos($row['store_logo'], '/') + 1) : null ,
                'logo_thumb' =>  isset($row['store_logo_thumb']) ? substr($row['store_logo_thumb'], strrpos($row['store_logo_thumb'], '/') + 1) : null ,
                'name' => utf8_encode($row['store_name']),
                'language' => $row['store_language'],
                'country' => $row['store_country']
                ]);

                app('MainService')->updateStoresVirtualRoutes($row['store_language'],$row['store_country']);

            }

        });
    }

    private function updateCategories($product_id, $categories)
    {
        $statement = 'DELETE FROM product_category WHERE product_id = ' . $product_id;
        \DB::statement($statement);
        \DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        $categories = explode(';', $categories);

        $categories = array_map(

            function ($v) {

                try {
                    $v = intval(trim($v));

                    if ($v > 0) {
                        return $v;
                    } else {
                        return null;
                    }

                } catch (\Exception $e) {
                    return null;
                }
            }

            , $categories
        );
        $categories = array_unique($categories);
        $data = [];

        foreach ($categories as $cat) {

            if ($cat) {
                $data[] = array(
                    'product_id' => $product_id,
                    'category_id' => $cat,
                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
                );
            }

        }

        \DB::table('product_category')->insert($data);
    }

}
