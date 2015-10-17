<?php
namespace AppBundle\Controller;

use FOS\RestBundle\Controller\ExceptionController as BaseController;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\Debug\Exception\FlattenException as DebugFlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\FlattenException as HttpFlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ExceptionController extends BaseController
{
    /**
     * Converts an Exception to a Response.
     *
     * @param Request                                    $request
     * @param HttpFlattenException|DebugFlattenException $exception
     * @param DebugLoggerInterface                       $logger
     * @param string                                     $format
     *
     * @return Response
     *
     * @throws \InvalidArgumentException
     */
    public function showAction(Request $request, $exception)
    {
        $currentContent = $this->getAndCleanOutputBuffering();
        $code = $this->getStatusCode($exception);
        $viewHandler = $this->container->get('fos_rest.view_handler');
        $parameters = $this->getParameters(
            $viewHandler,
            $currentContent,
            $code,
            $exception,
            $logger,
            $format
        );

        try {
            $response = $viewHandler->handle($view);
        } catch (\Exception $e) {
            $code = $this->getStatusCode($exception);
            $message = json_encode(['error' => ['code' => $code, 'message' => $this->getExceptionMessage($exception)]]);
            $response = new Response(
                $message,
                Codes::HTTP_INTERNAL_SERVER_ERROR,
                $exception->getHeaders());
        }

        return $response;
    }
}
