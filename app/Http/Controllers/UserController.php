<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use Hash;
use App\Todo;

class UserController extends Controller
{

    public function show($id, Request $request) {
        $this->validate($request, [
            'app' => 'required|in:todolite_android'
        ]);

        $user = User::findOrFail($id);
        $app = $request->input('app');
        $user->avatar = generateAvatarUrl($user->email);
        if ($app == 'todolite_android') {
            $user = userDataTodolite($user);
        }
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
            'offset' => 'integer|min:0',
            'limit' => 'integer|min:0',
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

    public function history(Request $request) {
        $token = $request->input('token');
        $user = validateUser($token);
        $this->validate($request, [
            'complete' => 'in:0,1',
            'abandon' => 'in:0,1',
            'types' => 'string',
            'offset' => 'integer|min:0',
            'limit' => 'integer|min:0',
            'keyword' => 'string',
            'year' => 'required|integer|between:2016,2099'
        ]);

        $complete = $request->input('complete', 1);
        $abandon = $request->input('abandon', 1);
        $types = $request->input('types');
        $offset =  $request->input('offset', 0);
        $limit = $request->input('limit', 50);
        $keyword = $request->input('keyword');
        $year = $request->input('year');

        $status = [];
        $complete == 1 && $status[] = 'complete';
        $abandon == 1 && $status[] = 'abandon';

        $builder = Todo::where('user_id', $user->id)
            ->whereIn('status', $status)
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
            })
            ->where('end_at', '>=', mktime(0, 0, 0, 1, 1, $year))
            ->where('end_at', '<', mktime(0, 0, 0, 1, 1, $year + 1));

        $all = $builder->count();

        $todolist = $builder
            ->orderBy('end_at', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();

        return response()->json(['all' => $all, 'todolist' => $todolist]);
    }
}
