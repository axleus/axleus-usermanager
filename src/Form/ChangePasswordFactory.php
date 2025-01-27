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
        $passwordOptions = $config[ConfigProvider::class][PasswordRequirement::class]['options'];
        $form = new ChangePassword(
            options: [
                'password_options' => $passwordOptions,
                'db_table_name'    => $config[ConfigProvider::class][ConfigProvider::DB_TABLE_NAME],
            ]
        );
        $form->setUrlHelper($container->get(UrlHelper::class));
        return $form;
    }
}
