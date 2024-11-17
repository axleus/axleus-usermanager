<?php

declare(strict_types=1);

namespace Axleus\UserManager\Handler;

use Axleus\UserManager\Form\ResetPassword;
use Axleus\UserManager\Helper\VerificationHelper;
use Laminas\Form\FormElementManager;
use Mezzio\Authentication\UserRepositoryInterface;
use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class ResetPasswordHandlerFactory
{
    public function __invoke(ContainerInterface $container) : ResetPasswordHandler
    {
        $manager = $container->get(FormElementManager::class);
        return new ResetPasswordHandler(
            $container->get(TemplateRendererInterface::class),
            $container->get(UserRepositoryInterface::class),
            $container->get(UrlHelper::class),
            $container->get(VerificationHelper::class),
            $manager->get(ResetPassword::class),
            $container->get('config')
        );
    }
}
