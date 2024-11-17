<?php

declare(strict_types=1);

namespace Axleus\UserManager\Form\Fieldset\Factory;

use Axleus\UserManager\Form\Fieldset\ProfileFieldset;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;


final class ProfileFieldsetFactory implements FactoryInterface
{
    /** @param string $requestedName */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ProfileFieldset
    {
        return new ProfileFieldset($container->get('config')['app_settings'], $options);
    }
}
