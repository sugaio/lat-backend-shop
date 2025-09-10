<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = $request->q;

        $users = User::latest()->when($q, function ($query) use ($q) {
            $query->where('name', 'LIKE', '%' . $q . '%');
        })->paginate(10);

        confirmDelete('Delete User!', 'Are you sure want to delete it?');
        return view('admin.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if(!$user) {
            Alert::error('Create Failed', 'Data Gagal Disimpan!');
            return redirect()->route('admin.user.index');
        }

            Alert::success('Create Successfully', 'Data Berhasil Disimpan!');
            return redirect()->route('admin.user.index');
        
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
    public function edit(User $user)
    {
        return view('admin.user.edit', compact('user'));        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
                $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);

        if($request->password == '') {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
        } else {
            $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            ]);
        }

        if(!$user) {
            Alert::error('Update Failed', 'Data Gagal Diperbaharui!');
            return redirect()->route('admin.user.index');
        }

            Alert::success('Update Successfully', 'Data Berhasil Diperbaharui!');
            return redirect()->route('admin.user.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        if(!$user) {
            Alert::error('Delete Failed', 'Data Gagal Dihapus!');
            return redirect()->route('admin.user.index');
        }

            Alert::success('Delete Successfully', 'Data Berhasil Dihapus!');
            return redirect()->route('admin.user.index');
    }
}
