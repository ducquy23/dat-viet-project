<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsPartner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth('partner')->check()) {
            return redirect('/')->with('error', 'Vui lòng đăng nhập để tiếp tục');
        }

        $user = auth('partner')->user();

        if ($user->role === 'admin') {
            return redirect('/admin')->with('error', 'Admin không thể đăng nhập vào site đối tác');
        }

        return $next($request);
    }
}


