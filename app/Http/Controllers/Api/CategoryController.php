<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data Categories',
            'categories' => $categories
        ]);
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)->first();

        if(!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Data Product by Category Tidak Ditemukan'
            ]);
        }
    
        return response()->json([
            'success' => true,
            'message' => 'List Product by Category'. $category->name,
            'product' => $category->products()->latest()->get(),
        ]);
    }

    public function categoryHeader()
    {
        $categories = Category::latest()->take(3)->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data Categories Header',
            'categories' => $categories
        ]);
    }
}
