<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Todo;
use App\Exceptions\MsgException;

class TodoController extends Controller
{
    public function show(Request $request, $id) {
    	$token = $request->input('token');
    	$user = validateUser($token);

    	$todo = Todo::findOrFail($id);

    	if ($user->id != $todo->user_id) {
    		throw new MsgException("You have not permission to view this item.", 403);
    	}
    	// $todo->contents = json_decode($todo->contents);
	    return response()->json($todo);
    }

    public function update(Request $request, $id) {
    	$token = $request->input('token');
    	$user = validateUser($token);

        

    	$this->validate($request, [
    		'title' => 'max:30',
    		'start_at' => 'integer',
    		'urgent_at' => 'integer',
    		'deadline' => 'integer',
    		'priority' => 'integer|between:1,9',
    		'location' => 'max:255'
    	]);

        $contents = $request->input('contents');

        if ($contents) {
            $this->validateContents($contents);
        }

    	$todo = Todo::findOrFail($id);

    	if ($todo->status != 0) {
    		throw new MsgException("Todo can only to be updated when status is to do.", 403);
    	}

    	$todo->update($request->except(['contents']));

        if ($contents) {
            $todo->contents = $this->limitContents($contents);
            $todo->save();
        }

    	return response()->json($todo);
    }

    public function store(Request $request) {
    	$token = $request->input('token');
    	$user = validateUser($token);

    	$this->validate($request, [
    		'title' => 'required|max:30',
    		'start_at' => 'required|integer',
    		'urgent_at' => 'integer',
    		'deadline' => 'integer',
    		'priority' => 'integer|between:1,9',
    		'location' => 'max:255'
    	]);

    	$contents = $request->input('contents', '[]');
        $this->validateContents($contents);

        $this->checkNotExceed($user->id);

    	$todo = Todo::create($request->except('contents'));

    	$todo->user_id = $user->id;
    	$todo->contents = $this->limitContents($contents);
    	$todo->save();

    	return response()->json($todo);
    }

    public function finish(Request $request, $id) {
    	$token = $request->input('token');
    	$user = validateUser($token);

    	$this->validate($request, [
    		'status' => 'string|in:complete,abandon'
    	]);

    	$status = $request->input('status', 'complete');

    	$todo = Todo::findOrFail($id);
    	if ($todo->status != 'todo') {
    		throw new MsgException("Todo can only to be completed when status is to do.", 403);
    	}

    	$todo->status = $status;
    	$todo->end_at = time();
    	$todo->save();

    	return response()->json($todo);
    }

    public function layside(Request $request, $id) {
    	$token = $request->input('token');
    	$user = validateUser($token);

        $this->validate($request, [
            'status' => 'string|in:todo,layside'
        ]);
        $status = $request->input('status', 'layside');

        $todo = Todo::findOrFail($id);
        if ($todo->status != 'todo' && $todo->status != 'layside') {
            throw new MsgException("Todo can only to be changed between to do and lay side.", 403);
        }

        if ($todo->status == $status) {
            throw new MsgException("You cannot change status to own status.", 400);
        }

        if ($status === 'layside') {
            $todo->end_at = time();
        } else {
            $todo->end_at = null;
        }
        $todo->status = $status;
        $todo->save();

        return response()->json($todo);
    }

    function limitContents($contents) {
    	return json_encode(array_slice(json_decode($contents), 0, 20));
    }

    function validateContents($contents) {
        $result = validateJson($contents, [
            '*.content' => 'required|string|max:255',
            '*.status' => 'required|in:0,1'
        ]);

        if (!$result) {
            throw new MsgException('Contents must be valid array of json with content and status.', 400);
        }
    }

    function checkNotExceed($userId) {
        $count = Todo::where('user_id', $userId)->where('status', 'todo')->count();
        if ($count >= 1000) {
            throw new MsgException("Too many todos, please complete them before add new one !!!", 499);
        }
    }
}
