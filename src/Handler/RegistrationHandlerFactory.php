<?php

declare(strict_types=1);

namespace Axleus\UserManager\Handler;

use Axleus\UserManager\Form\Register;
use Laminas\Form\FormElementManager;
use Mezzio\Authentication\UserRepositoryInterface;
use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class RegistrationHandlerFactory
{
    public function __invoke(ContainerInterface $container) : RegistrationHandler
    {
        $manager = $container->get(FormElementManager::class);
        return new RegistrationHandler(
            $container->get(TemplateRendererInterface::class),
            $container->get(UserRepositoryInterface::class),
            $manager->get(Register::class),
            $container->get(UrlHelper::class),
            $container->get('config')
        );
    }
}
