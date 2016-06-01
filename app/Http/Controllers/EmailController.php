<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\EmailVerification;
use Mail;

class EmailController extends Controller
{
    public function __construct() {

    }

    public function sendVerifyEmail(Request $request) {
		$this->validate($request, [
		    'email' => 'required|email'
		]);

		$email = $request->input('email');

		$verifys = EmailVerification::where('email', $email)->get();
		if(count($verifys) > 0) {
		    $verify = $verifys[0];
		} else {
            $verify = new EmailVerification;
		}

		$token = str_random(6);
		$send_time = time();
		$send_at = strftime('%Y-%m-%d %X', $send_time);

		// 一个小时后过期
		$avalible_before = strftime('%Y-%m-%d %X', $send_time + 3600);

		// 发送邮件
		Mail::send('emails.verification',
		  ['token' => $token, 'avalible_before' => $avalible_before],
		  function ($message) use($email) {
		    $message->to($email, 'dear')->subject('TodoLite Email verification');
		});

		// 将发送的验证码和过期时间保存到email_verifications表中
        $verify->email = $email;
        $verify->code = $token;
        $verify->send_at = $send_at;
        $verify->active = 1;
        $verify->save();

		return 'success';
	}
}
