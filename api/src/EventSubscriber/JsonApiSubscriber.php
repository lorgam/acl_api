<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\AcceptHeader;
use App\Controller\AbstractApiJsonController;

class JsonApiSubscriber implements EventSubscriberInterface
{
    private const CONTENT_TYPE = 'application/json';

    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if (!$controller instanceof AbstractApiJsonController) {
            return;
        }
        // Validate the Accept Header
        $request = $event->getRequest();
        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
        if ($acceptHeader->get(self::CONTENT_TYPE) == null) {
            throw new BadRequestHttpException('Header Accept not valid');
        }
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $response->headers->set('Accept', self::CONTENT_TYPE);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::RESPONSE => 'onKernelResponse'
        ];
    }
}
