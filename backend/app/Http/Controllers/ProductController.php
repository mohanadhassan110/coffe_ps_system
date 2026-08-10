<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Category;
use App\Models\Product;

/**
 * التحكم في إدارة المنتجات (CRUD)
 */
class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        Product::create($request->validated());
        return redirect()->route('products.index')
            ->with('success', __('messages.success.created'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(StoreProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        return redirect()->route('products.index')
            ->with('success', __('messages.success.updated'));
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')
            ->with('success', __('messages.success.deleted'));
    }
}
