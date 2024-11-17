<?php

declare(strict_types=1);

namespace Axleus\UserManager\Form\Fieldset\Factory;

use App\ConfigProvider as AppProvider;
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
        $passwordOptions = $container->get('config')[AppProvider::APP_SETTINGS_KEY][PasswordRequirement::class]['options'];
        $instance = new $requestedName(
            options: [
                'password_options' => $passwordOptions
            ]
        );

        if ($instance instanceof AdapterAwareInterface) {
            $instance->setDbAdapter($container->get(AdapterInterface::class));
        }

        return $instance;
    }
}
