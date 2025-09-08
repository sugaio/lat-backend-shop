<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = $request->q;
        $categories = Category::latest()->when($q, function ($query) use ($q) {
            $query->where('name', 'LIKE', '%'. $q . '%');
        })->paginate(10);

        confirmDelete('Delete Category!', 'Are you sure you want to delete');
        return view('admin.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view ('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'image' => 'required|image|mimes:png,jpg,jpeg,svg|max:2048',
            'name' => 'required|unique:categories'
        ]);

        $image = $request->file('image');
        $image->storeAs('categories', $image->hashName(), 'public');

        $category = Category::create([
            'image' => $image->hashName(),
            'name' => $request->name,
            'slug' => Str::slug($request->name,'-')
        ]);

        if(!$category) {
            Alert::success('Create Failed', 'Data Gagal Disimpan!');
            return redirect()->route('admin.category.index');
        }

        Alert::success('Create Successfully', 'Data Berhasil Disimpan!');
        return redirect()->route('admin.category.index');
    }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('admin.category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'  => 'required|unique:categories,name,' . $category->id
        ]);

        //check jika image kosong
        if ($request->file('image') == '') {

            //update data tanpa image
            $category = Category::findOrFail($category->id);
            $category->update([
                'name'   => $request->name,
                'slug'   => Str::slug($request->name, '-')
            ]);
        } else {

            //hapus image lama
            Storage::disk('public')->delete('categories/' . basename($category->image));

            //upload image baru
            $image = $request->file('image');
            $image->storeAs('categories', $image->hashName(), 'public');

            //update dengan image baru
            $category = Category::findOrFail($category->id);
            $category->update([
                'image'  => $image->hashName(),
                'name'   => $request->name,
                'slug'   => Str::slug($request->name, '-')
            ]);
        }

        if ($category) {
            Alert::success('Updated Successfully', 'Data Berhasil Diupdate!');
            return redirect()->route('admin.category.index');
        } else {
            Alert::error('Updated Failed', 'Data Gagal Diupdate!');
            return redirect()->route('admin.category.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if (!$category) {
            Alert::error('Delete Failed', 'Data Gagal Dihapus!');
            return redirect()->route('admin.category.index');
        }

        Storage::disk('public')->delete('categories/'.basename($category->image));
        $category->delete();

        Alert::success('Deleted Successfully', 'Data Berhasil Dihapus!');
        return redirect()->route('admin.category.index');
    }
}
