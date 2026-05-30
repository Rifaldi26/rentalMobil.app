<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // Redirect ke dashboard yang sesuai per role
                $redirect = match (true) {
                    $user->isAdmin()   => route('admin.dashboard'),
                    $user->isPartner() => route('partner.dashboard'),
                    default            => route('customer.bookings.index'),
                };

                return redirect($redirect);
            }
        }

        return $next($request);
    }
}
