<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::when($request->term, function ($query) use ($request) {
            $query->where('name', 'LIKE', '%'.$request->term.'%')->paginate();
                        
        }, function ($query) {
            $query->orderBy('id')->paginate()->map(fn ($product) => [
                'id' => $product->id,
                'name' => $product->name,
            ]);
        })->get();

         
        return Inertia::render(
            'Product/Index',
            [
                'products' => $products
            ]
        );
    }

     /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render(
            'Product/Create'
        );
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        $product = new Product();
        $product->name =$request->name;
        $product->status_id = 1;
        $product->save();
        sleep(1);

        return redirect()->route('product.index')->with('message', 'Produto Criado Com Sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return Inertia::render(
            'Product/Edit',
            [
                'product' => $product
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $product->name = $request->name;
        $product->save();
        sleep(1);

        return redirect()->route('product.index')->with('message', 'Produto atualizado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        sleep(1);

        return redirect()->route('product.index')->with('message', 'Produto deletado com sucesso');
    }
}
