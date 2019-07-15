<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class ApiAuthMiddleware
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
        $user_id = Redis::get($request->token);
        if (!$user_id) {
            return response()->json([
                'code' => 202,
                'msg' => '请登录'
            ]);
        }
        Auth::login(User::find($user_id));
        return $next($request);
    }
}
