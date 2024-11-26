<?php

declare(strict_types=1);

namespace Axleus\UserManager\Form\Fieldset\Factory;

use Axleus\UserManager\ConfigProvider;
use Axleus\UserManager\Form\Fieldset\ChangePasswordFieldset;
use Axleus\UserManager\Form\Fieldset\PasswordFieldset;
use Axleus\Validator\PasswordRequirement;
use Laminas\Db\Adapter\AdapterAwareInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;


final class PasswordFieldsetFactory implements FactoryInterface
{
    /** @inheritDoc */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): PasswordFieldset|ChangePasswordFieldset
    {
        $config          = $container->get('config');
        $passwordOptions = $config[ConfigProvider::class][PasswordRequirement::class]['options'];
        $instance        = new $requestedName(
            options: [
                'password_options' => $passwordOptions,
                'db_table_name'    => $config[ConfigProvider::class][ConfigProvider::DB_TABLE_NAME],
            ]
        );

        if ($instance instanceof AdapterAwareInterface) {
            $instance->setDbAdapter($container->get(AdapterInterface::class));
        }

        return $instance;
    }
}
