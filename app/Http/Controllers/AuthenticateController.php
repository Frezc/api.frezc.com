<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use Hash;

class AuthenticateController extends Controller
{

    function generateToken($uniqueStr) {
        return str_random(20).sha1($uniqueStr.time()).str_random(20);
    }

    public function authenticate(Request $request) {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|between:6,32',
        ]);

        $email = $request->Input('email');
        $password = $request->Input('password');

        $user = User::where('email', $email)->first();
        if ($user) {
            if (Hash::check($password, $user->password)) {
                return [
                    'user' => $user,
                    'token' => $this->generateToken($email)
                ];
            }
        }

        return null;
    }

    public function auth_todolite(Request $request) {
        $userWithToken = $this->authenticate($request);
        if ($userWithToken) {
            $userWithToken['user']->todo_app_token = $userWithToken['token'];
            $userWithToken['user']->save();
            return response()->json($userWithToken);
        }

        return response()->json(['error' => 'auth failed.'], 401);
    }

    public function register(Request $request) {
        $this->validate($request, [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|between:6,32',
            'nickname' => 'required|between:1,32'
        ]);

        $user = new User;
        $user->email = $request->input('email');
        $user->nickname = $request->input('nickname');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return 'success';
    }
}
