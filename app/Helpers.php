<?php
use App\EmailVerification;
use App\User;
use App\Exceptions\MsgException;
use Illuminate\Validation\ValidationException;

function clearVerification($email) {
	EmailVerification::where('email', $email)->delete();
	// DB::delete('delete from email_verifications where email = ?', [$email]);
}

function generateToken($uniqueStr) {
    return str_random(20).sha1($uniqueStr.time()).str_random(20);
}

function validateUser($token, $column = 'todo_app_token') {
	if ($token) {
		$users = User::where($column, $token)->get();

		$n_users = count($users);
		if ($n_users == 1) {
			return $users[0];
		} elseif ($n_users == 0) {
			throw new MsgException("Toke invalid", 401);
		} else {
			throw new MsgException("Token generation error", 500);
		}
	}

	throw new MsgException("Token not be provided", 400);
}

function validateJson($json, $rule) {
	$arr = json_decode($json, true);

	if (!is_array($arr)) {
		return false;
	}
	
	$v = Validator::make($arr, $rule);

	if ($v->fails()) {
		return false;
	}

	return true;
}
