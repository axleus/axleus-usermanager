<?php

declare(strict_types=1);

namespace Axleus\UserManager\Admin;

use Axleus\Admin\AdminContainer;
use Axleus\Admin\Event\AdminConnectEvent;
use Axleus\UserManager\User\UserRepository;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;

final class AdminConnectListener extends AbstractListenerAggregate
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            AdminConnectEvent::EVENT_ADMIN_CONNECT,
            [$this, 'onAdminConnect'],
            $priority
        );
    }

    public function onAdminConnect(AdminConnectEvent $event)
    {
        /** @var AdminContainer */
        $adminContainer = $event->getTarget();
        // mutate $adminContainer
        $adminContainer->offsetSet('data', ['userManager' => ['some_key' => 'some_value']]);
        return $adminContainer;
    }
}
