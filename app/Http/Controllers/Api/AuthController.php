<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:customers',
            'password' => 'required|confirmed'
        ]);

        $customer = Customer::create($validateData);
        $token = $customer->createToken($request->name);

        if(!$customer) {
            return response()->json([
                'success' => false,
            ], 409);
        }

        return response()->json([
            'success' => true,
            'customer' => $customer,
            'token' => $token->plainTextToken,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $customer = Customer::where('email', $request->email)->first();

    if (! $customer || ! Hash::check($request->password, $customer->password)) {
        return response()->json([
            'errors' => [
            'email' => ['The provided credentials are incorrect.'],
            ]
        ], 422);
    }

        $token = $customer->createToken($customer->name);

        return response()->json([
            'success' => true,
            'message' => 'Login customer successfully!',
            'customer' => $customer,
            'token' => $token->plainTextToken,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout customer successfully!'
        ]);
    }
}
