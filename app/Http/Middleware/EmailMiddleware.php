<?php

namespace App\Http\Middleware;

use Closure;
use Validator;
use App\EmailVerification;

class EmailMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $v = Validator::make($request->all(), [
            'email' => 'required|email|exists:email_verifications,email',
            'code' => 'required|string'
        ]);

        if ($v->fails()){
            return response()->json($v->errors(), 400);
        }

        $email = $request->input('email');
        $code = $request->input('code');

        $veri = EmailVerification::where('email', $email)->first();

        if ($veri == null || $veri->active != 1 || $veri->code != $code){
            return response()->json(['error' => 'wrong code'], 430);
        }

        if (abs(time() - strtotime($veri->send_at)) > 3600) {
            return response()->json(['error' => 'time exceed'], 431);
        }

        $response = $next($request);
        if ($response->getStatusCode() == 200) {
          $veri->active = 0;
          $veri->save();
        }
        // clearVerification($request->input('email'));

        return $response;
    }
}
