<?php

namespace App\Helpers;

use stdClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\MessageBag;

class ResponseHandler
{

    /**
     * success
     *
     * @param  array|object $body
     * @param  string|null  $message
     * @return JsonResponse
     */
    public static function success(array|object $body = [], ?string $message = null): JsonResponse
    {
        $message = $message ?? __('messages.general.success');
        return self::send(200, message: $message, body: $body);
    }

    /**
     * validationError
     *
     * @param  MessageBag   $validationErrors
     * @param  string|null  $message
     * @return JsonResponse
     */
    public static function validationError(MessageBag $validationErrors = null, ?string $message = null): JsonResponse
    {
        $message = $message ?? __('messages.general.validation');
        return self::send(422, message: $message, errors: $validationErrors);
    }

    /**
     * authenticationError
     *
     * @param  string|null  $message
     * @return JsonResponse
     */
    public static function authenticationError(?string $message = null): JsonResponse
    {
        $message = $message ?? __('messages.general.unauthenticated');
        return self::send(401, message: $message);
    }

    /**
     * notFound
     * @return JsonResponse
     */
    public static function notFound(): JsonResponse
    {
        $message = __('messages.general.notfound');
        return self::send(404, message: $message);
    }

    /**
     * failure
     *
     * @param  string|null  $message
     * @param  object       $exception
     * @return JsonResponse
     */
    public static function failure(?string $message = null, object $exception = null): JsonResponse
    {
        $message = $message ?? __('messages.general.failed');

        if ($exception) {
            $exception = array(
                'line'  => $exception->getLine(),
                'file'  => $exception->getFile(),
                'message' => $exception->getMessage()

            );
        }
        return self::send(400, message: $message, exception: $exception);
    }


    /**
     * send
     *
     * @param  int          $status
     * @param  string|null  $message
     * @param  array|object $body
     * @param  array        $errors
     * @param  array        $exception
     * @return JsonResponse
     */
    private static function send(int $status, string $message, array|object $body = null, MessageBag $errors = null, null|array $exception = []): JsonResponse
    {

        $response =  [
            'status_code'   =>  $status,
            'message'       =>  $message,
            'body'          =>  $body ?? new stdClass,
            'errors'        =>  $errors ?? new stdClass,
            'exception'     =>  $exception,
        ];

        return response()->json($response, $status, [], JSON_UNESCAPED_UNICODE);
    }
}

