<?php

declare(strict_types=1);

namespace Axleus\UserManager\Handler;

use Axleus\UserManager\Form\ChangePassword;
use Axleus\UserManager\Helper\VerificationHelper;
use Axleus\UserManager\User\UserRepository;
use Laminas\Form\FormElementManager;
use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class ChangePasswordHandlerFactory
{
    public function __invoke(ContainerInterface $container): ChangePasswordHandler
    {
        $fm = $container->get(FormElementManager::class);

        return new ChangePasswordHandler(
            $container->get(TemplateRendererInterface::class),
            $container->get(UserRepository::class),
            $fm->get(ChangePassword::class),
            $container->get(VerificationHelper::class),
            $container->get(UrlHelper::class),
            $container->get('config')
        );
    }
}
