<?php

namespace App\Http\Middleware;

use App\Models\DetailLogin;
use App\Models\DetailsLogin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CheckMidnightLogout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
 
        public function handle($request, Closure $next)
        {
            if (Auth::check()) {
                $user = Auth::user();
                $loginInfo = DetailLogin::where('user_id', $user->id)->first();
    
                if ($loginInfo) {
                    $loginDate = Carbon::parse($loginInfo->login_date)->toDateString();
                    $currentDate = Carbon::now()->toDateString();
    
                    // Check if it's a new day since the last login
                    if ($loginDate !== $currentDate) {
                        Auth::guard('web')->logout(); 
                        return redirect('/login')->withErrors(['message' => 'You login session has expired. Please log in again.']);
                    }
                }
            }
        return $next($request);
    }
}
