<?php
namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class AuthIdListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if ($request->query->get('AuthID') != 'TASK24H-TEST') {
            $json = json_encode(['error' => ['code' => 403, 'message' => 'Authentication Failed']]);
            $event->setResponse(new Response($json, 403));
        }
    }
}
