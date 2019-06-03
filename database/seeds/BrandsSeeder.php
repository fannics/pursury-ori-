<?php

use Illuminate\Database\Seeder;
use ProjectCarrasco\Brand;
use ProjectCarrasco\Product;

class BrandsSeeder extends Seeder
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
            $brand = Brand::where('title',$product->brand)
                          ->where('country' , $product->country)
                          ->where('language',$product->language)->first();

            if(is_null($brand))
            {
                $brand = Brand::create([
                    'title' => $product->brand,
                    'country' => $product->country,
                    'language' => $product->language
                ]);
            }

            $product->brand = $brand->id;
            $product->save();

        }
    }
}
