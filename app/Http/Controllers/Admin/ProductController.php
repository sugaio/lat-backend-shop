<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = $request->q;

        $products = Product::latest()->when($q, function ($query) use ($q) {
            $query->where('name', 'LIKE', '%' , $q . '%');
        })->paginate(10);

        confirmDelete('Delete Product!', 'Are you sure want to delete?');
        return view('admin.product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::latest()->get();
        return view('admin.product.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'image' => 'required|image|mimes:png,jpg,jpeg,svg|max:2048',
            'title' => 'required|unique:products',
            'category_id'    => 'required',
            'weight'         => 'required',
            'description'    => 'required',
            'price'          => 'required',
            'stock'          => 'required',
            'discount'       => 'nullable',
        ]);

        $image = $request->file('image');
        $image->storeAs('products', $image->hashName(), 'public');

        $product = Product::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'slug' => Str::slug($request->title,'-'),
            'category_id'    => $request->category_id,
            'weight'         => $request->weight,
            'description'    => $request->description,
            'price'          => $request->price,
            'stock'          => $request->stock,
            'discount'       => $request->discount,
        ]);

        if(!$product) {
            Alert::success('Create Failed', 'Product Gagal Disimpan!');
            return redirect()->route('admin.product.index');
        }

        Alert::success('Create Successfully', 'Product Berhasil Disimpan!');
        return redirect()->route('admin.product.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::latest()->get();
        return view('admin.product.edit', compact('categories', 'product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
         // dd($request);
        $request->validate([
            'title'          => 'required|unique:products,title,' .$product->id,
            'category_id'    => 'required',
            'weight'         => 'required',
            'description'    => 'required',
            'price'          => 'required',
            'stock'          => 'required',
            'discount'       => 'nullable',
        ]);

        if($request->file('image') == '') {
            $product->update ([
            'title'          => $request->title,
            'slug'           => Str::slug($request->title,'-'),
            'category_id'    => $request->category_id,
            'weight'         => $request->weight,
            'description'    => $request->description,
            'price'          => $request->price,
            'stock'          => $request->stock,
            'discount'       => $request->discount,
            ]);
        } else {
            Storage::disk('public')->delete('products/'.basename($product->image));

            $image = $request->file('image');
            $image->storeAs('products', $image->hashName(), 'public');

            //save to db
            $product->update ([
            'image'          => $image->hashName(),
            'title'          => $request->title,
            'slug'           => Str::slug($request->title,'-'),
            'category_id'    => $request->category_id,
            'weight'         => $request->weight,
            'description'    => $request->description,
            'price'          => $request->price,
            'stock'          => $request->stock,
            'discount'       => $request->discount,
            ]);
        }

        if(!$product) {
            Alert::success('Update Failed', 'Product Gagal Diedit!');
            return redirect()->route('admin.product.index');
        }

        Alert::success('Update Successfully', 'Product Berhasil Diedit!');
        return redirect()->route('admin.product.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
            if (!$product) {
            Alert::error('Delete Failed', 'Data Gagal Dihapus!');
            return redirect()->route('admin.product.index');
        }

        Storage::disk('public')->delete('products/'.basename($product->image));
        $product->delete();

        Alert::success('Deleted Successfully', 'Data Berhasil Dihapus!');
        return redirect()->route('admin.product.index');
    }
}
