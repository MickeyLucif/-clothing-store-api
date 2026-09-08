<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Constants\Http\HttpErrorCode;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ValidationErrorResource;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

final class ExceptionRenderer
{
    public function __invoke(Throwable $exception, Request $request): ?JsonResponse
    {
        if ($exception instanceof DomainException) {
            return null;
        }

        if (! $request->is('api/*') && ! $request->expectsJson()) {
            return null;
        }

        return match (true) {
            $exception instanceof ValidationException => $this->validation($exception),
            $exception instanceof AuthenticationException => $this->error(
                Response::HTTP_UNAUTHORIZED,
                'Unauthenticated.',
                HttpErrorCode::UNAUTHENTICATED,
            ),
            $exception instanceof TokenExpiredException => $this->error(
                Response::HTTP_UNAUTHORIZED,
                'Token has expired.',
                HttpErrorCode::TOKEN_EXPIRED,
            ),
            $exception instanceof TokenBlacklistedException => $this->error(
                Response::HTTP_UNAUTHORIZED,
                'Token has been blacklisted.',
                HttpErrorCode::TOKEN_BLACKLISTED,
            ),
            $exception instanceof TokenInvalidException => $this->error(
                Response::HTTP_UNAUTHORIZED,
                'Token is invalid.',
                HttpErrorCode::TOKEN_INVALID,
            ),
            $exception instanceof JWTException => $this->error(
                Response::HTTP_UNAUTHORIZED,
                'Token could not be processed.',
                HttpErrorCode::TOKEN_INVALID,
            ),
            $exception instanceof HttpExceptionInterface => $this->http($exception),
            default => $this->server($exception),
        };
    }

    private function validation(ValidationException $exception): JsonResponse
    {
        return new JsonResponse(
            data: ValidationErrorResource::make($exception->getMessage(), $exception->errors()),
            status: Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    private function http(HttpExceptionInterface $exception): JsonResponse
    {
        $status = $exception->getStatusCode();

        return new JsonResponse(
            data: ErrorResource::make(
                $status,
                $exception->getMessage() !== ''
                    ? $exception->getMessage()
                    : (Response::$statusTexts[$status] ?? 'HTTP error.'),
                $this->codeForStatus($status),
            ),
            status: $status,
            headers: $exception->getHeaders(),
        );
    }

    private function server(Throwable $exception): JsonResponse
    {
        return new JsonResponse(
            data: ErrorResource::make(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') === true ? $exception->getMessage() : 'Server error.',
                HttpErrorCode::SERVER_ERROR,
            ),
            status: Response::HTTP_INTERNAL_SERVER_ERROR,
        );
    }

    private function error(int $status, string $message, string $domainError): JsonResponse
    {
        return new JsonResponse(
            data: ErrorResource::make($status, $message, $domainError),
            status: $status,
        );
    }

    private function codeForStatus(int $status): string
    {
        return match (true) {
            $status === Response::HTTP_UNAUTHORIZED => HttpErrorCode::UNAUTHENTICATED,
            $status === Response::HTTP_FORBIDDEN => HttpErrorCode::FORBIDDEN,
            $status === Response::HTTP_NOT_FOUND => HttpErrorCode::NOT_FOUND,
            $status === Response::HTTP_METHOD_NOT_ALLOWED => HttpErrorCode::METHOD_NOT_ALLOWED,
            $status === Response::HTTP_TOO_MANY_REQUESTS => HttpErrorCode::TOO_MANY_REQUESTS,
            $status >= Response::HTTP_INTERNAL_SERVER_ERROR => HttpErrorCode::SERVER_ERROR,
            default => HttpErrorCode::HTTP_ERROR,
        };
    }
}
