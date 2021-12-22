<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ErrorFormat",
 *     @OA\Property(property="error", type="string", description="Error message"),
 * )
 */
class ErrorController extends AbstractApiController
{
    public function handleError(\Throwable $exception)
    {
        $message = $exception->getMessage();
        switch(true) {
            case $exception instanceof HttpExceptionInterface :
                $code = $exception->getStatusCode();

                // Convert ParamConverter message
                if (preg_match('/@ParamConverter/', $message)) {
                    $message = 'Resource not found';
                }
                break;
            default:
                $code = isset(Response::$statusTexts[$exception->getCode()])
                    ? $exception->getCode()
                    : Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return $this->json(['error' => $message], $code);
    }
}