<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Lib\Util;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    public function signup(Request $request){
         $validatedData = $request->validate([

            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);


        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
    $expirationMinutes = 10080;


    $cookie = Util::create($token, $expirationMinutes, 'none');
        return response()->json([
            'message' => 'Signup successful',
            'user' => $user,
        ])->cookie($cookie);
        }

   public function login(Request $request){
    $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        $expirationMinutes = 10080;
    $cookie = Util::create($token, $expirationMinutes, 'none');
        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
        ])->cookie($cookie);
   }
   public function logout(Request $request){
   if ($request->cookie('api_token')) {

    $expiredCookie = Util::forget();

    return response()->json([
        'message' => 'Successfully logged out.'
    ], 200)->withCookie($expiredCookie);

}

// no cookie case
return response()->json([
    'message' => 'There is no token.'
], 200);
   }
   public function checkAuth(Request $request){
    return response()->json([
        'message' => 'Authenticated',
        'user' => $request->user(),
    ]);
   }
}

