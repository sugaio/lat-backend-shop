<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
        public function index(Request $request)
    {
        $q = $request->q;

        $customers = Customer::latest()->when($q, function ($query) use ($q) {
            $query->where('name', 'LIKE', '%' , $q . '%');
        })->paginate(10);

        return view('admin.customer.index', compact('customers'));
    }
}
