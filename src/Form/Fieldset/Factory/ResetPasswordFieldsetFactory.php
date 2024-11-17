<?php

declare(strict_types=1);

namespace Axleus\UserManager\Form\Fieldset\Factory;

use Axleus\UserManager\ConfigProvider;
use Axleus\UserManager\Form\Fieldset\ResetPasswordFieldset;
use Laminas\Db\Adapter\AdapterInterface;
use Psr\Container\ContainerInterface;

final class ResetPasswordFieldsetFactory
{
    public function __invoke(ContainerInterface $container): ResetPasswordFieldset
    {
        $config = $container->get('config');
        $fieldset = new ResetPasswordFieldset(
            targetTable: $config[ConfigProvider::class][ConfigProvider::USERMANAGER_TABLE_NAME],
            targetColumn: $config['authentication']['username']
        );
        $fieldset->setDbAdapter($container->get(AdapterInterface::class));
        return $fieldset;
    }
}
