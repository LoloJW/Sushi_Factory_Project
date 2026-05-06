<?php

namespace App\EventListener;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\RouterInterface;

final class AccessDeniedHandlerListener
{
    public function __construct(
        private Security $security,
        private RouterInterface $router,
    ) {
    }

    #[AsEventListener]
    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof AccessDeniedHttpException) {
            return;
        }

        if ($this->security->getUser()) {
            $event->setResponse(
                new RedirectResponse($this->router->generate('app_welcome'))
            );
        } else {
            $event->setResponse(
                new RedirectResponse($this->router->generate('app_login'))
            );
        }
    }
}
