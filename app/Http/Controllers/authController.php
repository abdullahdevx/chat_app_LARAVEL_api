<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class authController extends Controller
{
    public function registerUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:60',
            'email' => 'required|unique:users',
            'password' => 'required|string',
        ]);
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);
        $sanctumToken = $user->createToken('userToken')->plainTextToken;
        $response = [
            'user' => $user,
            'token' => $sanctumToken,
            'message' => 'Registered Successfully',
        ];

        return response($response, 201);
    }

    public function loginUser(Request $request)
    {
        $credientials = $request->only('email', 'password');

        if(Auth::attempt($credientials))
        {
            $user = Auth::user();
            $sanctumToken = $user->createToken('userToken')->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $sanctumToken,
             ];
            return response()->json($response, 200);
        }
        else
        {
            $response = ['status' => 'Invalid Credientials'];
            return response($response, 401);
        }
    }

    public function logoutUser(Request $request)
    {
        $user = $request->user();

        $user->tokens()->delete();
        return response()->json(['message' => 'LOGGED OUT SUCCESSFULLY', 200]);

    }


    
}
