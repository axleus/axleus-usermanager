<?php

declare(strict_types=1);

namespace Axleus\UserManager\Admin;

use Axleus\Admin\Event\AdminConnectEvent;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;

final class AdminConnectListener extends AbstractListenerAggregate
{

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
        $adminContainer = $event->getTarget();
        // mutate $adminContainer
        return $adminContainer;
    }
}
