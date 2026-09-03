<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use App\Exceptions\ApiException;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request, Throwable $exception): bool =>
            $request->is('api/*') || $request->expectsJson()
        );
        $exceptions->render(
            function (ApiException $exception): JsonResponse {
                return new JsonResponse(
                    data: [
                        'error' => [
                            'code' => $exception->errorCode,
                            'message' => $exception->getMessage()
                        ]
                    ],
                    status: $exception->statusCode,
                );
            }
        );
    })->create();
