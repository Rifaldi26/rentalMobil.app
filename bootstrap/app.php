<?php

use App\Exceptions\BookingConflictException;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\UnauthorizedBookingException;
use App\Exceptions\VehicleNotAvailableException;
use App\Http\Middleware\EnsureNotSuspended;
use App\Http\Middleware\IsAdmin;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web:      __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health:   '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin'         => IsAdmin::class,
            'not.suspended' => EnsureNotSuspended::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReport([
            VehicleNotAvailableException::class,
            BookingConflictException::class,
            UnauthorizedBookingException::class,
            InsufficientBalanceException::class,
        ]);

        $exceptions->renderable(function (VehicleNotAvailableException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], Response::HTTP_CONFLICT);
            }
            return back()->with('error', $e->getMessage());
        });

        $exceptions->renderable(function (BookingConflictException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], Response::HTTP_CONFLICT);
            }
            return back()->withInput()->with('error', $e->getMessage());
        });

        $exceptions->renderable(function (UnauthorizedBookingException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], Response::HTTP_FORBIDDEN);
            }
            return back()->with('error', $e->getMessage());
        });

        $exceptions->renderable(function (InsufficientBalanceException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            return back()->withErrors(['amount' => $e->getMessage()]);
        });

        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->guest(route('login'));
        });
    })
    ->create();