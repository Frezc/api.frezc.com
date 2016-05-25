<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use Hash;
use App\Todo;

class UserController extends Controller
{

    public function show($id) {
        $user = User::findOrFail($id);
        return response()->json($user);
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

    public function todolist(Request $request) {
        $token = $request->input('token');
        $user = validateUser($token);
        $this->validate($request, [
            'status' => 'string',
            'types' => 'string',
            'orderBy' => 'in:created_at,updated_at,start_at,priority,end_at',
            'direction' => 'in:asc,desc',
            'offset' => 'integer',
            'limit' => 'integer',
            'keyword' => 'string'
        ]);

        $status = $request->input('status');
        $types = $request->input('types');
        $orderBy = $request->input('orderBy', 'updated_at');
        $direction = $request->input('direction', 'desc');
        $offset =  $request->input('offset', 0);
        $limit = $request->input('limit', 1000);
        $keyword = $request->input('keyword');

        $builder = Todo::where('user_id', $user->id)
            ->when($status, function($query) use($status) {
                $status_arr = explode(",", trim($status));
                return $query->whereIn('status', $status_arr);
            })
            ->when($types, function($query) use($types) {
                $types_arr = explode(",", trim($types));
                return $query->whereIn('type', $types_arr);
            })
            ->when($keyword, function($query) use($keyword) {
                return 
                $query->where(function($query) use($keyword) {
                    $query->where('title', 'like', '%'.$keyword.'%')
                          ->orWhere('location', 'like', '%'.$keyword.'%');
                });
            });

        $all = $builder->count();

        $todolist = $builder
            ->orderBy($orderBy, $direction)
            ->skip($offset)
            ->take($limit)
            ->get();

        return response()->json(['all' => $all, 'todolist' => $todolist]);
    }
}
