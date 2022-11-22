<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->renderable(function (RuntimeException $e, $request) {
            if ($this->shouldReturnJson($request, $e)) {
                return response()->json($this->convertExceptionToArray($e), $e->status());
            }

            return $this->prepareResponse(
                $request, config('app.debug') ? $e : new HttpException($e->status(), $e->getMessage(), $e)
            );
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->shouldReturnJson($request, $exception)
                    ? response()->json([
                        'errcode' => 401,
                        'message' => $exception->getMessage(),
                    ], 401)
                    : redirect()->guest($exception->redirectTo() ?? route('login'));
    }

    /**
     * {@inheritdoc}
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        $errors = $exception->errors();

        if (is_array($message = Arr::first($errors))) {
            $message = Arr::first($message);
        }

        return response()->json([
            'errcode' => 422,
            'message' => $message ?: '参数错误',
            'errors' => $errors,
        ], $exception->status);
    }

    /**
     * {@inheritdoc}
     */
    protected function convertExceptionToArray(Throwable $e)
    {
        if ($e instanceof RuntimeException) {
            return [
                'errcode' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        } elseif ($this->isHttpException($e)) {
            $data = [
                'errcode' => $e->getStatusCode(),
                'message' => $e->getMessage(),
            ];
        } else {
            $data = [
                'errcode' => 500,
                'message' => config('app.debug') ? $e->getMessage() : '服务器错误',
            ];
        }

        return array_merge(parent::convertExceptionToArray($e), $data);
    }
}
