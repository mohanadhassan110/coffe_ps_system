<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

/**
 * التحكم في إدارة التصنيفات (CRUD)
 */
class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:categories']);
        Category::create($request->only('name'));
        return redirect()->route('categories.index')
            ->with('success', __('messages.success.created'));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate(['name' => 'required|string|max:255|unique:categories,name,' . $category->id]);
        $category->update($request->only('name'));
        return redirect()->route('categories.index')
            ->with('success', __('messages.success.updated'));
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', __('messages.errors.delete_has_products'));
        }

        $category->delete();
        return redirect()->route('categories.index')
            ->with('success', __('messages.success.deleted'));
    }
}
