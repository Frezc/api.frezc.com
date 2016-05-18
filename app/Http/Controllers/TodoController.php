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

    	$request->contents = json_decode($request->contents, true);
    	$this->validate($request, [
    		'title' => 'max:30',
    		'start_at' => 'integer',
    		'urgent_at' => 'integer',
    		'deadline' => 'integer',
    		'priority' => 'integer|between:1,9',
    		'location' => 'max:255',
    		'contents' => 'json',
    		'contents.*.content' => 'required|string|max:255',
    		'contents.*.status' => 'required|in:0,1'
    	]);

    	$contents = $request->input('contents');

    	$todo = Todo::findOrFail($id);

    	if ($todo->status != 0) {
    		throw new MsgException("Todo can only to be updated when status is to do.", 403);
    	}

    	$todo->update($request->except(['contents']));

    	$todo->contents = $this->validateContents($contents);
    	$todo->save();

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
    		'location' => 'max:255',
    		'contents' => 'json',
    		'contents.*.content' => 'required|max:255',
    		'contents.*.status' => 'required|in:0,1'
    	]);

    	$contents = $request->input('contents', '[]');

    	$count = Todo::where('user_id', $user->id)->where('status', 0)->count();
    	if ($count >= 1000) {
    		throw new MsgException("Too many todos, please complete them before add new one !!!", 499);
    	}

    	$todo = Todo::create($request->except('contents'));

    	$todo->user_id = $user->id;
    	$todo->contents = $this->validateContents($contents);
    	$todo->save();

    	return response()->json($todo);
    }

    public function finish(Request $request, $id) {
    	$token = $request->input('token');
    	$user = validateUser($token);

    	$this->validate($request, [
    		'type' => 'string|in:complete,abandon'
    	]);

    	$type = $request->input('type', 'complete');

    	$todo = Todo::findOrFail($id);
    	if ($todo->status != 0) {
    		throw new MsgException("Todo can only to be completed when status is to do.", 403);
    	}

    	$status = $type === 'abandon' ? 3 : 1;
    	$todo->status = $status;
    	$todo->end_at = time();
    	$todo->save();

    	return response()->json($todo);
    }

    public function layside(Request $request, $id) {
    	$token = $request->input('token');
    	$user = validateUser($token);

        $this->validate($request, [
            'type' => 'string|in:todo,layside'
        ]);
        $type = $request->input('type', 'layside');

        $todo = Todo::findOrFail($id);
        if ($todo->status != 0 && $todo->status != 2) {
            throw new MsgException("Todo can only to be changed between todo and layside.", 403);
        }

        if ($type === 'layside') {
            $status = 2;
            $todo->end_at = time();
        } else {
            $status = 0;
            $todo->end_at = null;
        }
        $todo->status = $status;
        $todo->save();

        return response()->json($todo);
    }

    function validateContents($contents) {
    	return json_encode(array_slice(json_decode($contents), 0, 20));
    }
}
