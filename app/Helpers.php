<?php
use App\EmailVerification;
use App\User;
use App\Todo;
use App\Exceptions\MsgException;
use Illuminate\Validation\ValidationException;

function clearVerification($email) {
	EmailVerification::where('email', $email)->delete();
	// DB::delete('delete from email_verifications where email = ?', [$email]);
}

function generateToken($uniqueStr) {
    return str_random(20).sha1($uniqueStr.time()).str_random(20);
}

function validateUser($token, $column = 'todolite_android') {
	if ($token) {
		$userBuilder = User::where($column, $token);

		$n_users = $userBuilder->count();
		if ($n_users == 1) {
			return $userBuilder->first();
		} elseif ($n_users == 0) {
			throw new MsgException("Toke invalid", 401);
		} elseif ($n_users > 1) {
			$userBuilder->update([
				$column => null
			]);
			throw new MsgException("Token generation error, please re-auth", 500);
		}
	}

	throw new MsgException("Token not be provided", 401);
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

function generateAvatarUrl($email, $size = 120) {
	return 'https://cdn.v2ex.co/gravatar/'.md5(strtolower(trim($email))).'?d=retro&r=pg&s='.$size;
}

function userDataTodolite(User $user) {
	$builder = Todo::where('user_id', $user->id);
	$user->todo = $builder->where('status', 'todo')->count();
	$user->layside = $builder->where('status', 'layside')->count();
	$user->complete = $builder->where('status', 'complete')->count();
	$user->abandon = $builder->where('status', 'abandon')->count();
	return $user;
}
