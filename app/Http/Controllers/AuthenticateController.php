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
            'app' => 'required|in:todolite_android'
        ]);

        $email = $request->input('email');
        $password = $request->input('password');
        $app = $request->input('app');

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
            $token = generateToken($email.$app);
            $user->{$app} = $token;
            $user->save();
            $user->avatar = generateAvatarUrl($email);
            return response()->json([
                'user' => $user, 
                'token' => $token
            ]);
        }
        
        return response()->json(['error' => 'authenticate failed.'], 401);
    }

    public function refresh(Request $request) {
        $this->validate($request, [
            'token' => 'required',
            'app' => 'required|in:todolite_android'
        ]);

        $token = $request->input('token');
        $app = $request->input('app');

        $user = validateUser($token, $app);

        $token = generateToken($user->email.$app);
        $user->{$app} = $token;
        $user->save();
        $user->avatar = generateAvatarUrl($user->email);
        return response()->json(['user' => $user, 'token' => $token]);
    }

    public function unauth(Request $request) {
        $this->validate($request, [
            'token' => 'required',
            'app' => 'required|in:todolite_android'
        ]);

        $token = $request->input('token');
        $app = $request->input('app');

        $user = validateUser($token, $app);
        $user->{$app} = null;
        $user->save();
        return 'success';
    }
}
