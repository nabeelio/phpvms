<?php

namespace App\Exceptions;

use Illuminate\Validation\ValidationException;
use Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        #\Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException
            && $exception->getStatusCode() == 403) {
            return redirect()->guest('login');
        }

        if($request->is('api/*')) {

            $error = [
                'error' => [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTrace()[0],
                ]
            ];

            $status = 400;
            $http_code = $exception->getCode();
            if ($this->isHttpException($exception)) {
                $status = $exception->getStatusCode();
                $http_code = $exception->getStatusCode();
            }

            if($exception instanceof ModelNotFoundException) {
                $status = 404;
                $http_code = 404;
            }

            if($exception instanceof ValidationException) {
                $status = 400;
                $http_code = 400;
                $error['error']['failedRules'] = $exception->validator->failed();
            }

            Log::error($exception->getMessage());

            $error['error']['http_code'] = $http_code;
            return response()->json($error, $status);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }

    /**
     * Render the given HttpException.
     */
    protected function renderHttpException(\Symfony\Component\HttpKernel\Exception\HttpException $e)
    {
        $status = $e->getStatusCode();
        view()->replaceNamespace('errors', [
            resource_path('views/layouts/' . config('phpvms.skin') . '/errors'),
            resource_path('views/errors'),
            __DIR__ . '/views',
        ]);

        #Log::info('error status '. $status);

        if (view()->exists("errors::{$status}")) {
        #if (view()->exists('layouts' . config('phpvms.skin') .'.errors.' .$status)) {
            return response()->view("errors::{$status}", ['exception' => $e], $status, $e->getHeaders());
        } else {
            return $this->convertExceptionToResponse($e);
        }
    }
}
