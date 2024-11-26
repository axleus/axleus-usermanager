<?php

declare(strict_types=1);

namespace Axleus\UserManager\InputFilter;

use Axleus\UserManager\ConfigProvider;
use Axleus\Validator\PasswordRequirement;
use Laminas\Db\Adapter\AdapterInterface;
use Psr\Container\ContainerInterface;

final class AcctDataFilterFactory
{
    public function __invoke(ContainerInterface $container): AcctDataFilter
    {
        $config = $container->get('config');
        $filter = new AcctDataFilter(
            $config[ConfigProvider::class][ConfigProvider::DB_TABLE_NAME],
            $config['authentication']['username'],
            $config[ConfigProvider::class][PasswordRequirement::class]['options']
        );
        $filter->setDbAdapter($container->get(AdapterInterface::class));
        return $filter;
    }
}
