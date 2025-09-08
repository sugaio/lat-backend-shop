<?php

namespace App\Http\Controllers\Admin;

use App\Models\Slider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class SliderController extends Controller
{
        public function index()
    {
        $sliders = Slider::latest()->paginate(5);

        confirmDelete('Delete Slider!', 'Are you sure you want to delete?');
        return view('admin.slider.index', compact('sliders'));
    }

    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'image' => 'required|image|mimes:png,jpg,jpeg,svg|max:2048',
            'title' => 'required',
            'description' => 'nullable',
            'link' => 'required'
        ]);

        $image = $request->file('image');
        $image->storeAs('sliders', $image->hashName(), 'public');

        $slider = Slider::create([
            'image'  => $image->hashName(),
            'title'  => $request->title,
            'description' => $request->description,
            'link'   => $request->link
        ]);

        if(!$slider) {
            Alert::success('Create Failed', 'Data Gagal Disimpan!');
            return redirect()->route('admin.slider.index');
        }

        Alert::success('Create Successfully', 'Data Berhasil Disimpan!');
        return redirect()->route('admin.slider.index');
    }

        public function destroy(Slider $silder)
    {
        if (!$silder) {
            Alert::error('Delete Failed', 'Data Gagal Dihapus!');
            return redirect()->route('admin.slider.index');
        }

        Storage::disk('public')->delete('categories/'.basename($silder->image));
        $silder->delete();

        Alert::success('Deleted Successfully', 'Data Berhasil Dihapus!');
        return redirect()->route('admin.slider.index');
    }

}
