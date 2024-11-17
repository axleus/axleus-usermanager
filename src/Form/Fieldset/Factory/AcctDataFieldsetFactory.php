<?php

declare(strict_types=1);

namespace Axleus\UserManager\Form\Fieldset\Factory;

use Axleus\UserManager\Form\Fieldset\AcctDataFieldset;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

final class AcctDataFieldsetFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param null|mixed[] $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): AcctDataFieldset
    {
        return new AcctDataFieldset();
    }
}
