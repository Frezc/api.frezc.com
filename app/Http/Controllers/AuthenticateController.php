<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;

class AuthenticateController extends Controller
{
    public function authenticate(Request $request) {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|between:6,32',
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

/*
        $user = User::where('email', $email)->first();
        if ($user) {
            if (Hash::check($password, $user->password)) {
                return [
                    'user' => $user,
                    'token' => generateToken($email)
                ];
            }
        }
*/

        if (Auth::once(['email' => $email, 'password' => $password])) {
            $user = Auth::user();
            return [
                'user' => $user,
                'token' => generateToken($email)
            ];
        }
        
        return null;
    }

    public function auth_todolite(Request $request) {
        $userWithToken = $this->authenticate($request);
        if ($userWithToken) {
            $userWithToken['user']->todo_app_token = $userWithToken['token'];
            $userWithToken['user']->save();
            $userWithToken['user']->avatar = generateAvatarUrl($userWithToken['user']->email);
            return response()->json($userWithToken);
        }

        return response()->json(['error' => 'authenticate failed.'], 401);
    }

    public function refresh_todolite(Request $request) {
        $token = $request->input('token');
        $user = validateUser($token);

        $user->todo_app_token = generateToken($user->email);
        $user->save();
        $user->avatar = generateAvatarUrl($user->email);
        return response()->json(['user' => $user, 'token' => $user->todo_app_token]);
    }
}
