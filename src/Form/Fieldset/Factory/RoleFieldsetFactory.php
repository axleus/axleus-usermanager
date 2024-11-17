<?php

declare(strict_types=1);

namespace UserManager\Form\Fieldset\Factory;

use Alxeus\ConfigProvider;
use Axleus\UserManager\Form\Fieldset\RoleFieldset;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class RoleFieldsetFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): RoleFieldset
    {
        /**
         * todo: Pull config from container
         * Target correct config key to pull known roles from config
         */
        return new RoleFieldset($container->get(Roles::class));
    }
}
