<?php

use Illuminate\Database\Seeder;
use ProjectCarrasco\Store;
use ProjectCarrasco\Product;

class StoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::all();

        foreach ($products as $product)
        {
            $store = Store::where('name',$product->store)
                          ->where('country' , $product->country)
                          ->where('language',$product->language)->first();

            if(is_null($store))
            {
                $store = Store::create([
                    'name' => $product->store,
                    'country' => $product->country,
                    'language' => $product->language
                ]);
            }

            $product->store = $store->id;
            $product->save();

        }
    }
}
