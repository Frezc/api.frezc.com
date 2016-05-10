<?php
use App\EmailVerification;

function clearVerification($email) {
	EmailVerification::where('email', $email)->delete();
	// DB::delete('delete from email_verifications where email = ?', [$email]);
}