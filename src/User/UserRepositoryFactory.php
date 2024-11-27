<?php

declare(strict_types=1);

namespace Axleus\UserManager\User;

use Axleus\Db;
use Axleus\UserManager\ConfigProvider;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Hydrator\ArraySerializableHydrator;
use Mezzio\Authentication\UserInterface;
use Psr\Container\ContainerInterface;

final class UserRepositoryFactory
{
    public function __invoke(ContainerInterface $container): UserRepository
    {
        $config   = $container->get('config')[ConfigProvider::class];
        $hydrator = new ArraySerializableHydrator();
        return new UserRepository(
            gateway: new Db\TableGateway(
                $config[ConfigProvider::DB_TABLE_NAME],
                $container->get(AdapterInterface::class),
                null,
                new HydratingResultSet(
                    $hydrator,
                    new UserEntity()
                )
            ),
            userFactory: $container->get(UserInterface::class),
            hydrator: $hydrator,
            config: $container->get('config')['authentication']
        );
    }
}
