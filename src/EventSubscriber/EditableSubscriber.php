<?php

// src/EventSubscriber/TokenSubscriber.php
namespace App\EventSubscriber;

use App\Entity\User;
use App\Interface\EditableControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Controller\ErrorController;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class EditableSubscriber implements EventSubscriberInterface
{
    public function __construct(private Security $security) {}

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        // when a controller class defines multiple action methods, the controller
        // is returned as [$controllerInstance, 'methodName']
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        $request = $event->getRequest();
        $hasEditParam = $request->query->has('edit');
        if ($controller instanceof EditableControllerInterface && $hasEditParam) {
            $edit = $request->query->getBoolean('edit', false);
            if (true === $edit) {
                $user = $this->security->getUser();
                if (!$user instanceof User || false === $user->isAdmin()) {
                    // put query parameter to false to prevent unauthorized access
                    $request->query->set('edit', false);
                }
            }
        }
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $edit = $request->query->get('edit');
        $referer = $request->headers->get('referer');

        if (null !== $edit || !$request->isMethod('GET') || $this->isLiveComponentRequest($request)) {
            return;
        }

        if (!$referer || !str_contains($referer, 'edit=1')) {
            return;
        }


        $qs = $request->getQueryString();
        $newQs = $qs ? $qs . '&edit=1' : 'edit=1';
        $url = $request->getUri() . '?' . $newQs;
        $event->setResponse(new RedirectResponse($url, 302));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    private function isLiveComponentRequest(Request $request): bool
    {
        return $request->attributes->get('_route') && str_starts_with($request->attributes->get('_route'), 'ux_live_component');
    }
}
