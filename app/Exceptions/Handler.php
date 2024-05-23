<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'fcm_token',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $e) {
            if ($e instanceof NotFoundHttpException) {
                if (request()->wantsJson() || request()->is('api/*')) {
                    return response()->json(['status' => false, 'message' => __('exception.not_found')])
                        ->setStatusCode(Response::HTTP_NOT_FOUND);
                }

                if (request()->routeIs('selection.show')) {
                    return response()->view(
                        'errors.404',
                        ['message' => 'Sorry, this selection has been removed'],
                        Response::HTTP_NOT_FOUND
                    );
                }
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                return response()->json(['status' => false, 'message' => __('exception.not_allowed')])
                    ->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
            }

            if ($e instanceof AccessDeniedHttpException) {
                return response()->json(['status' => false, 'message' => __('exception.unauthorized')])
                    ->setStatusCode(Response::HTTP_FORBIDDEN);
            }
        });
    }
}
