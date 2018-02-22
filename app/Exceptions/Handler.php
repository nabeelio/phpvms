<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class Handler
 * @package App\Exceptions
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     * @param  \Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Create an error message
     * @param $status_code
     * @param $message
     * @return array
     */
    protected function createError($status_code, $message)
    {
        return [
            'error' => [
                'status' => $status_code,
                'message' => $message,
            ]
        ];
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return mixed
     */
    public function render($request, Exception $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {

            $headers = [];

            Log::error('API Error', $exception->getTrace());

            if($exception instanceof ModelNotFoundException ||
                $exception instanceof NotFoundHttpException) {
                $error = $this->createError(404, $exception->getMessage());
            }

            # Custom exceptions should be extending HttpException
            elseif ($exception instanceof HttpException) {
                $error = $this->createError(
                    $exception->getStatusCode(),
                    $exception->getMessage()
                );

                $headers = $exception->getHeaders();
            }

            # Create the detailed errors from the validation errors
            elseif($exception instanceof ValidationException) {
                $error_messages = [];
                $errors = $exception->errors();
                foreach($errors as $field => $error) {
                    $error_messages[] = implode(', ', $error);
                }

                $message = implode(', ', $error_messages);
                $error = $this->createError(400, $message);
                $error['error']['errors'] = $errors;

                Log::error('Validation errors', $errors);
            }

            else {
                $error = $this->createError(400, $exception->getMessage());
            }

            # Only add trace if in dev
            if(config('app.env') === 'dev') {
                $error['error']['trace'] = $exception->getTrace()[0];
            }

            return response()->json($error, $error['error']['status'], $headers);
        }

        if ($exception instanceof HttpException
            && $exception->getStatusCode() === 403) {
            return redirect()->guest('login');
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
        if ($request->expectsJson() || $request->is('api/*')) {
            $error = $this->createError(401, 'Unauthenticated');
            return response()->json($error, 401);
        }

        return redirect()->guest('login');
    }

    /**
     * Render the given HttpException.
     * @param HttpException $e
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    protected function renderHttpException(HttpException $e)
    {
        $status = $e->getStatusCode();
        view()->replaceNamespace('errors', [
            resource_path('views/layouts/' . config('phpvms.skin') . '/errors'),
            resource_path('views/errors'),
            __DIR__ . '/views',
        ]);

        if (view()->exists("errors::{$status}")) {
        #if (view()->exists('layouts' . config('phpvms.skin') .'.errors.' .$status)) {
            return response()->view("errors::{$status}", [
                'exception' => $e,
                'SKIN_NAME' => config('phpvms.skin'),
            ], $status, $e->getHeaders());
        } else {
            return $this->convertExceptionToResponse($e);
        }
    }
}
