<?php

declare(strict_types=1);

namespace Axleus\UserManager\Handler;

use Axleus\UserManager\Form\ResendVerification;
use Axleus\UserManager\Helper\VerificationHelper;
use Laminas\Form\FormElementManager;
use Mezzio\Authentication\UserRepositoryInterface;
use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class VerifyAccountHandlerFactory
{
    public function __invoke(ContainerInterface $container): VerifyAccountHandler
    {
        $manager = $container->get(FormElementManager::class);
        return new VerifyAccountHandler(
            $container->get(TemplateRendererInterface::class),
            $container->get(UserRepositoryInterface::class),
            $container->get(VerificationHelper::class),
            $manager->get(ResendVerification::class),
            $container->get(UrlHelper::class),
            $container->get('config')
        );
    }
}
