<?php

namespace App\Http\Middleware;

use App\Models\DetailLogin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SingleSessionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            $sessionToken = session('custom_session_token');

            // Check if the stored session ID matches the current session
            if ($user->session_id !== $sessionToken) {
                Auth::guard('web')->logout();  // Log out the user if the session ID does not match
                return redirect()->route('login')->withErrors([
                    'message' => 'You have been logged out due to a new login from another device.'
                ]);
            }
        }
        return $next($request);
    }
}
