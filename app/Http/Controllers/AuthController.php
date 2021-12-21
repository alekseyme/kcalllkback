<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('username', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Неверный логин или пароль'
            ], 400);
        }
        
        $token = $user->createToken($user->username.'_token')->plainTextToken;

        $cookie = cookie('stkn', $token, 60*24);

        return response()->json([
            'name' => $user->name,
            'username' => $user->username,
            'projects' => $user->projects()->count(),
            'manager' => $user->manager()->first(),
            'isadmin' => $user->isadmin,
        ], 200)->withCookie($cookie);
    }

    public function me()
    {
        return response()->json([
            'name' => auth()->user()->name,
            'username' => auth()->user()->username,
            'projects' => auth()->user()->projects()->count(),
            'manager' => auth()->user()->manager()->first(),
            'isadmin' => auth()->user()->isadmin,
        ]);
        // return auth()->user();
    }

    public function logout()
    {
        // auth()->user()->tokens()->delete();
        $cookie = Cookie::forget('stkn');

        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Успешный выход'
        ], 200)->withCookie($cookie);
    }
}
