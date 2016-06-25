<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\Exceptions\MsgException;
use Hash;

class AuthenticateController extends Controller
{
    public function authenticate(Request $request) {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|between:6,32',
            'app' => $this->appRule
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
            $user = bindData($user, $app);
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
            'app' => $this->appRule
        ]);

        $token = $request->input('token');
        $app = $request->input('app');

        $user = validateUser($token, $app);

        $token = generateToken($user->email.$app);
        $user->{$app} = $token;
        $user->save();
        $user = bindData($user, $app);
        return response()->json(['user' => $user, 'token' => $token]);
    }

    public function unauth(Request $request) {
        $this->validate($request, [
            'token' => 'required',
            'app' => $this->appRule
        ]);

        $token = $request->input('token');
        $app = $request->input('app');

        $user = validateUser($token, $app);
        $user->{$app} = null;
        $user->save();
        return 'success';
    }

    public function resetPassword(Request $request) {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|between:6,32'
        ]);
        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $email)->first();
        if (!$user) {
            throw new MsgException("This account is not exist.", 400);
        }

        $user->password = Hash::make($password);
        foreach ($this->apps as $app) {
            $user->{$app} = null;
        }
        $user->save();
        return 'success';
    }
}
