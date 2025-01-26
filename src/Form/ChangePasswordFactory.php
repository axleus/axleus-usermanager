<?php

declare(strict_types=1);

namespace Axleus\UserManager\Form;

use Axleus\UserManager\ConfigProvider;
use Axleus\Validator\PasswordRequirement;
use Mezzio\Helper\UrlHelper;
use Psr\Container\ContainerInterface;

final class ChangePasswordFactory
{
    public function __invoke(ContainerInterface $container): ChangePassword
    {
        $config          = $container->get('config');
        $tableName       = $config[ConfigProvider::class][ConfigProvider::DB_TABLE_NAME];
        $passwordOptions = $config[ConfigProvider::class][PasswordRequirement::class]['options'];
        $form = new ChangePassword(
            options: [
                ConfigProvider::DB_TABLE_NAME => $tableName,
                'password_options' => $passwordOptions,
            ]
        );
        $form->setUrlHelper($container->get(UrlHelper::class));
        return $form;
    }
}
