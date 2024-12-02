<?php

declare(strict_types=1);

namespace Axleus\UserManager\Admin;

use Axleus\UserManager\User\UserRepository;
use Psr\Container\ContainerInterface;

final class AdminConnectListenerFactory
{
    public function __invoke(ContainerInterface $container): AdminConnectListener
    {
        // todo: be a little more defensive here
        return new AdminConnectListener($container->get(UserRepository::class));
    }
}
