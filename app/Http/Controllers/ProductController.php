<?php

namespace App\Http\Controllers;

class ProductController extends Controller
{
    public function index()
    {
        return view('products.index', ['products' => config('products')]);
    }

    public function show(string $slug)
    {
        $product = config("products.$slug");

        abort_if($product === null, 404);

        return view("products.$slug", [
            'product' => $product,
            'others' => collect(config('products'))->except($slug)->all(),
        ]);
    }
}
