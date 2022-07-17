<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only([
            'store',
            'update',
            'destroy'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products =  Product::orderBy('name')->get();

        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required',
        ]);

        try {
            $product = Product::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name, '-'),
                'description' => $request->description,
                'price' => $request->price,
            ]);

            return response()->json($product, 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found.'
            ], 404);
        }

        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found.'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string',
            'price' => 'required',
        ]);

        try {
            $product->fill($request->all());
            $product->slug = Str::slug($request->name, '-');
            $product->save();

            return response()->json($product);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found.'
            ], 404);
        }

        try {
            $product->save();

            return response()->json([
                'message' => 'Product deleted.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search the specified resource in storage.
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $products = Product::where('name', 'like', '%' . $request->name . '%')->get();

        return response()->json($products);
    }
}
