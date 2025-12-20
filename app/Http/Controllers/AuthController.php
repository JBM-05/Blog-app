<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Cookie;
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


    $cookie = Cookie::make('api_token', $token, $expirationMinutes, null, null, false, true);
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
    $cookie = Cookie::make('api_token', $token, $expirationMinutes, null, null, false, true);
        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
        ])->cookie($cookie);
   }
   public function logout(Request $request){
   if ($request->cookie('api_token')) {

    $expiredCookie = Cookie::forget('api_token');

    return response()->json([
        'message' => 'Successfully logged out.'
    ], 200)->withCookie($expiredCookie);

}
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
   public function updateProfile(Request $request){
    $user = $request->user();

    $validatedData = $request->validate([
        'name' => 'sometimes|required|string|max:255',
        'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
        'password' => 'sometimes|required|string|min:8',
        'avatar' => 'sometimes|required|image|max:2048',
    ]);

    if (isset($validatedData['name'])) {
        $user->name = $validatedData['name'];
    }
    if (isset($validatedData['email'])) {
        $user->email = $validatedData['email'];
    }
    if (isset($validatedData['password'])) {
        $user->password = bcrypt($validatedData['password']);
    }
    if (isset($validatedData['avatar'])) {
    CloudinaryService::delete($user->avatar_public_id);


    $upload = CloudinaryService::upload(
        $request->file('avatar'),
        'avatars',
    );

    $user->avatar_url = $upload['url'];
    $user->avatar_public_id = $upload['public_id'];
    }

    $user->save();

    return response()->json([
        'message' => 'Profile updated successfully',
        'user' => $user,
    ]);
   }
}

