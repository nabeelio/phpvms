<?php

namespace App\Exceptions;

use App\Exceptions\Converters\GenericException;
use App\Exceptions\Converters\NotFound;
use App\Exceptions\Converters\SymfonyException;
use App\Exceptions\Converters\ValidationException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException as SymfonyHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Whoops\Handler\HandlerInterface;

/**
 * Class Handler
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     */
    protected $dontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        HttpException::class,
        IlluminateValidationException::class,
        ModelNotFoundException::class,
        SymfonyHttpException::class,
        TokenMismatchException::class,
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request   $request
     * @param Exception $exception
     *
     * @return mixed
     */
    public function render($request, Exception $exception)
    {
        if ($request->is('api/*')) {
            Log::error('API Error', $exception->getTrace());

            if ($exception instanceof HttpException) {
                return $exception->getResponse();
            }

            /*
             * Not of the HttpException abstract class. Map these into
             */

            if ($exception instanceof ModelNotFoundException ||
                $exception instanceof NotFoundHttpException) {
                $error = new NotFound($exception);
                return $error->getResponse();
            }

            // Custom exceptions should be extending HttpException
            if ($exception instanceof SymfonyHttpException) {
                $error = new SymfonyException($exception);
                return $error->getResponse();
            }

            // Create the detailed errors from the validation errors
            if ($exception instanceof IlluminateValidationException) {
                $error = new ValidationException($exception);
                return $error->getResponse();
            }

            $error = new GenericException($exception);
            return $error->getResponse();
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
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            $error = new Unauthenticated();
            return $error->getResponse();
        }

        return redirect()->guest('login');
    }

    /**
     * Render the given HttpException.
     *
     * @param HttpException $e
     *
     * @return \Illuminate\Http\Response|Response
     */
    protected function renderHttpException(HttpExceptionInterface $e)
    {
        $status = $e->getStatusCode();
        view()->replaceNamespace('errors', [
            resource_path('views/layouts/'.config('phpvms.skin').'/errors'),
            resource_path('views/errors'),
            __DIR__.'/views',
        ]);

        if (view()->exists("errors::{$status}")) {
            //if (view()->exists('layouts' . config('phpvms.skin') .'.errors.' .$status)) {
            return response()->view("errors::{$status}", [
                'exception' => $e,
                'SKIN_NAME' => config('phpvms.skin'),
            ], $status, $e->getHeaders());
        }

        return $this->convertExceptionToResponse($e);
    }

    /**
     * Ignition error page integration
     */
    protected function whoopsHandler()
    {
        try {
            return app(HandlerInterface::class);
        } catch (BindingResolutionException $e) {
            return parent::whoopsHandler();
        }
    }
}
