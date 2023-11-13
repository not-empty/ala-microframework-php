<?php

namespace App\Exceptions;

use App\Exceptions\Custom;
use App\Exceptions\Custom\DuplicatedDataException;
use App\Exceptions\Custom\InputValidationException;
use App\Exceptions\Custom\ValidationException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use ResponseJson\ResponseJson;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Ulid\Ulid;

class Handler extends ExceptionHandler
{
    private $response;
    private $request;

    /**
     * A list of the exception types that should not be reported.
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        DataNotFoundException::class,
        FilterException::class,
        HttpException::class,
        InvalidCredentialsException::class,
        ModelNotFoundException::class,
        NotAuthorizedException::class,
        ValidationCustom::class,
        ValidationException::class,
        SuffixRequiredException::class,
        RouteNotFoundException::class,
    ];

    /**
     * constructor
     * @param ResponseJson $response
     * @param Request $request
     * @return void
     */
    public function __construct(
        ResponseJson $response,
        Request $request
    ) {
        $this->response = $response;
        $this->request = $request;
    }

    /**
     * report bug
     * @param  Throwable $exception
     * @return void
     * @throws Exception
     */
    public function report(
        Exception $exception
    ) {
        if ($this->shouldntReport($exception)) {
            return;
        }

        if (extension_loaded('newrelic') && env('APP_ENV') == 'production') {
            $this->request->newRelic->noticeError($exception->getMessage(), $exception);
        }

        parent::report($exception);
    }

     /**
     * render bug and return with json response
     * @param  Request $request
     * @param  Exception $exception
     * @return JsonResponse
     * @throws Exception
     */
    public function render(
        $request,
        Exception $exception
    ) {
        $requestId = $request->requestId ?? '';
        $startProfile = $request->startProfile ?? 0;

        if ($exception instanceof ValidationException) {
            $result = $this->response->response(
                $requestId,
                $startProfile,
                $request->jwtToken,
                $exception->getMessages(),
                'A validation error occurrs'
            );

            return response()->json(
                $result,
                422,
                [],
                JSON_UNESCAPED_SLASHES |
                JSON_UNESCAPED_UNICODE
            );
        }

        if ($exception instanceof HttpException) {
            $result = $this->response->response(
                $requestId,
                $startProfile,
                $request->jwtToken,
                [],
                'Route not found'
            );

            return response()->json(
                $result,
                404,
                [],
                JSON_UNESCAPED_SLASHES |
                JSON_UNESCAPED_UNICODE
            );
        }

        $code = $exception->getCode() ?? 500;
        if (!is_int($code) || $code > 505 || $code <= 0) {
            $code = 500;
        }

        $message = $exception->getMessage();
        if ($code == 500) {
            if (ENV('APP_DEBUG') == 'true') {
                dd($exception);
            }

            $message = 'An unexpected error occurred, please try again later';
        }

        $result = $this->response->response(
            $requestId,
            $startProfile,
            $request->jwtToken,
            [],
            $message,
            $code
        );

        return response()->json(
            $result,
            $code,
            [],
            JSON_UNESCAPED_SLASHES |
            JSON_UNESCAPED_UNICODE
        );
    }
}
