<?php

declare(strict_types=1);

namespace Axleus\UserManager\Form;

use App\ConfigProvider as AppProvider;
use Axleus\Validator\PasswordRequirement;
use Mezzio\Helper\UrlHelper;
use Psr\Container\ContainerInterface;

final class ChangePasswordFactory
{
    public function __invoke(ContainerInterface $container): ChangePassword
    {
        $passwordOptions = $container->get('config')[AppProvider::APP_SETTINGS_KEY][PasswordRequirement::class]['options'];
        $form = new ChangePassword(
            options: [
                'password_options' => $passwordOptions,
            ]
        );
        $form->setUrlHelper($container->get(UrlHelper::class));
        return $form;
    }
}
