<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\NimingbanId;
use App\Models\NimingbanBranch;
use App\Models\NimingbanReply;

class NimingbanController extends Controller
{
    

    public function id(Request $request) {
    	$oldId = $request->input('oldId');
    	if ($oldId) {
    		$ids = NimingbanId::where('uniqueId', $oldId)->get();
    		if (count($ids) == 1) {
    			// update updated_at
    			// $ids[0]->touch();
    			return response()->json(['id' => $ids[0]->uniqueId]);
    		}
    	}

    	$newId = new NimingbanId;
    	$newId->save();
    	$newId->uniqueId = generateId($newId->id);
    	$newId->save();
    	return response()->json(['id' => $newId->uniqueId]);
    }

    public function getBranches(Request $request) {
		$this->validate($request, [
    		'section' => 'required'
    	]);

    	$section = $request->input('section');
    	$offset = $request->input('offset', 0);
    	$limit = $request->input('limit', 10);
    	$withReplies = $request->input('withReplies', 3);

    	$branches = NimingbanBranch::where('section', $section)
    					->orderBy('updated_at', 'desc');
    	$all = $branches->count();

    	$branches = $branches
    				->skip($offset)
	            	->take($limit)
	            	->get();
	    foreach ($branches as $branch) {
	    	$replies = NimingbanReply::where('branchId', $branch->id)->orderBy('floor', 'asc');
	    	$branch->repliesNum = $replies->count();
	    	$skip = $branch->repliesNum - $withReplies;
	    	$branch->replies = $replies->skip($skip)->limit($withReplies)->get();
	    }

	    return response()->json(['all' => $all, 'branches' => $branches]);
    }

    public function createBranches(Request $request) {
    	$this->validate($request, [
    		'section' => 'required',
    		'authorId' => 'required',
    		'content' => 'required'
    	]);

    	return response()->json(NimingbanBranch::create($request->all()));
    }

    public function branch(Request $request, $id) {
    	$withReplies = $request->input('withReplies', 20);

    	$branch = NimingbanBranch::findOrFail($id);
    	$replies = NimingbanReply::where('branchId', $branch->id)->orderBy('floor', 'asc');
    	$branch->repliesNum = $replies->count();
    	$branch->replies = $replies->limit($withReplies)->get();

    	return response()->json($branch);
    }

    public function getReplies(Request $request, $id) {
    	$offset = $request->input('offset', 0);
    	$limit = $request->input('limit', 20);

    	$replies = NimingbanReply::where('branchId', $id)->orderBy('floor', 'asc');
    	$all = $replies->count();
    	$replies = $replies->skip($offset)->limit($limit)->get();

    	return response()->json(['all' => $all, 'replies' => $replies]);
    }

    public function createReply(Request $request, $id) {
    	$this->validate($request, [
    		'authorId' => 'required',
    		'content' => 'required'
    	]);

    	$replyToFloor = (int)$request->input('replyToFloor', 0);

    	$replies = NimingbanReply::where('branchId', $id);

    	$recentReply = $replies->orderBy('floor', 'desc')->first();
    	if ($recentReply) $floor = $recentReply->floor + 1;
    	else $floor = 2;

    	$replyTo = $replies->where('floor', $replyToFloor)->first();
    	if ($replyTo) $replyToId = $replyTo->id;
    	else $replyToId = 0;

    	$reply = new NimingbanReply;
    	$reply->branchId = intval($id);
    	$reply->authorId = $request->input('authorId');
    	$reply->content = $request->input('content');
    	$reply->authorName = $request->input('authorName');
    	$reply->floor = $floor;
    	$reply->replyToId = $replyToId;
    	$reply->replyToFloor = $replyToFloor;
    	$reply->save();

    	return response()->json($reply);
    }
}
