<?php

declare(strict_types=1);

namespace Axleus\UserManager;

use Axleus\Core\ConfigProviderInterface;
use Axleus\Core\Middleware\AuthorizedHandlerPipelineDelegator;
use Axleus\Mailer\ConfigProvider as MailConfigProvider;
use Axleus\Mailer\Adapter\AdapterInterface;
use Axleus\Mailer\Middleware\MailerMiddleware;
use Axleus\Validator\PasswordRequirement;
use Fig\Http\Message\RequestMethodInterface as Http;
use Laminas\Permissions\Acl\Assertion\OwnershipAssertion;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Mezzio\Authentication\AuthenticationInterface;
use Mezzio\Authentication\Session\PhpSession;
use Mezzio\Authentication\UserRepositoryInterface;
use Mezzio\Helper\BodyParams\BodyParamsMiddleware;

final class ConfigProvider implements ConfigProviderInterface
{
    public const CONFIG_MANAGER_TARGET_FILE = 'usermanager.global.php';
    public const MODULE_NAME                      = 'module_name';
    public const DB_TABLE_NAME                    = 'db_table_name';
    public const APPEND_HTTP_METHOD_TO_PERMS      = 'append_http_method_to_permissions';
    public const APPEND_ONLY_MAPPED               = 'append_only_mapped';
    public const MAIL_MESSAGE_TEMPLATES           = 'message_templates';
    public const MAIL_VERIFY_MESSAGE_BODY         = 'verify_message_body';
    public const MAIL_VERIFY_SUBJECT              = 'verify_message_subject';
    public const MAIL_RESET_PASSWORD_MESSAGE_BODY = 'reset_password_message_body';
    public const MAIL_RESET_PASSWORD_SUBJECT      = 'reset_password_message_subject';
    public const TOKEN_KEY                        = 'token_lifetime';
    public const RBAC_MAPPED_ROUTES               = 'rbac_mapped_routes';

    public function __invoke(): array
    {
        return [
            static::class              => $this->getAxleusConfig(),
            'authentication'           => $this->getAuthenticationConfig(),
            'dependencies'             => $this->getDependencies(),
            'form_elements'            => $this->getFormElementConfig(),
            'input_filters'            => $this->getInputFilterConfig(),
            'listeners'                => $this->getListenerConfig(),
            'mezzio-authorization-acl' => $this->getAuthorizationConfig(),
            'navigation'               => $this->getNavigation(),
            'routes'                   => $this->getRouteConfig(),
            'templates'                => $this->getTemplates(),
            'view_helpers'             => $this->getViewHelpers(),
            MailConfigProvider::class  => $this->getMailConfig(),
        ];
    }

    public function getAuthorizationConfig(): array
    {
        return [
            'resources' => [
                'account.read',
                'change.password',
                'login',
                'logout',
                'register',
                'reset.password',
                'verify.account',
            ],
            'allow'     => [
                'Guest' => [
                    'login',
                ],
                'User'  => [
                    'logout',
                    ['account.read', 'assertion' => new OwnershipAssertion()],
                ],
                'Administrator' => [
                ],
            ],
            'deny' => [
                'User' => [
                    'login'
                ],
            ],
        ];
    }

    public function getAxleusConfig(): array
    {
        return [
            static::MODULE_NAME                 => 'Axleus UserManager',
            static::DB_TABLE_NAME               => 'user',
            static::APPEND_HTTP_METHOD_TO_PERMS => true, // bool true|false
            static::APPEND_ONLY_MAPPED          => true, // bool true|false
            //static::RBAC_MAPPED_ROUTES          => $this->getRbacMappedRoutes(), // array of routes to map http methods to
            'token_lifetime'                    => [
                'verificationToken'   => '1 Hour',
                'passwordResetToken'  => '1 Hour',
            ],
            PasswordRequirement::class          => [
                'options' => [
                    'length'  => 8, // overall length of password
                    'upper'   => 1, // uppercase count
                    'lower'   => 2, // lowercase count
                    'digit'   => 2, // digit count
                    'special' => 2, // special char count
                ],
            ],
            MailConfigProvider::class => $this->getMailConfig(),
        ];
    }

    public function getAuthenticationConfig(): array
    {
        return [
            'redirect' => '/user-manager/account', // redirect for authentication component post login
            'username' => 'email',
            'password' => 'password',
        ];
    }

    public function getDependencies(): array
    {
        return [
            'aliases'    => [
                AuthenticationInterface::class => PhpSession::class,
                UserRepositoryInterface::class => User\UserRepository::class,
            ],
            'delegators' => [
                Handler\AccountHandler::class        => [
                    AuthorizedHandlerPipelineDelegator::class,
                ],
                Handler\ChangePasswordHandler::class => [
                    AuthorizedHandlerPipelineDelegator::class,
                ],
                Handler\LoginHandler::class          => [
                    AuthorizedHandlerPipelineDelegator::class,
                ],
                Handler\LogoutHandler::class         => [
                    AuthorizedHandlerPipelineDelegator::class,
                ],
                Handler\RegistrationHandler::class   => [
                    AuthorizedHandlerPipelineDelegator::class,
                ],
                Handler\ResetPasswordHandler::class  => [
                    AuthorizedHandlerPipelineDelegator::class,
                ],
                Handler\VerifyAccountHandler::class  => [
                    AuthorizedHandlerPipelineDelegator::class,
                ],
            ],
            'factories'  => [
                Admin\AdminConnectListener::class        => Admin\AdminConnectListenerFactory::class,
                Handler\AccountHandler::class            => Handler\AccountHandlerFactory::class,
                Handler\ChangePasswordHandler::class     => Handler\ChangePasswordHandlerFactory::class,
                Handler\LoginHandler::class              => Handler\LoginHandlerFactory::class,
                Handler\LogoutHandler::class             => Handler\LogoutHandlerFactory::class,
                Handler\RegistrationHandler::class       => Handler\RegistrationHandlerFactory::class,
                Handler\ResetPasswordHandler::class      => Handler\ResetPasswordHandlerFactory::class,
                Handler\VerifyAccountHandler::class      => Handler\VerifyAccountHandlerFactory::class,
                Helper\VerificationHelper::class         => Helper\VerificationHelperFactory::class,
                Message\Listener\MessageListener::class  => Message\Listener\MessageListenerFactory::class,
                Middleware\IdentityMiddleware::class     => Middleware\IdentityMiddlewareFactory::class,
                User\UserRepository::class               => User\UserRepositoryFactory::class,
            ],
        ];
    }

    public function getFormElementConfig(): array
    {
        return [
            'factories' => [
                Form\Fieldset\AcctDataFieldset::class       => Form\Fieldset\Factory\AcctDataFieldsetFactory::class,
                Form\Fieldset\ChangePasswordFieldset::class => Form\Fieldset\Factory\PasswordFieldsetFactory::class,
                Form\Fieldset\PasswordFieldset::class       => Form\Fieldset\Factory\PasswordFieldsetFactory::class,
                Form\Fieldset\ResendVerification::class     => InvokableFactory::class,
                Form\ChangePassword::class                  => Form\ChangePasswordFactory::class,
                Form\Login::class                           => Form\LoginFactory::class,
                Form\Register::class                        => Form\RegisterFactory::class,
                Form\ResendVerification::class              => Form\ResendVerificationFactory::class,
                Form\ResetPassword::class                   => Form\ResetPasswordFactory::class,
                Form\Fieldset\ResetPasswordFieldset::class  => Form\Fieldset\Factory\ResetPasswordFieldsetFactory::class,
            ],
        ];
    }

    public function getInputFilterConfig(): array
    {
        return [
            'factories' => [
                InputFilter\AcctDataFilter::class => InputFilter\AcctDataFilterFactory::class,
            ],
        ];
    }

    public function getListenerConfig(): array
    {
        return [
            Admin\AdminConnectListener::class,
            Message\Listener\MessageListener::class,
        ];
    }

    public function getMailConfig(): array
    {
        return [
            AdapterInterface::class => [
                static::MAIL_MESSAGE_TEMPLATES => [
                    static::MAIL_VERIFY_SUBJECT         => '%s Account Verification.',
                    static::MAIL_VERIFY_MESSAGE_BODY    => 'Please click the link to verify your email address. The link is valid for %s. Please <a href="%s%s">Click Here!!</a>',
                    static::MAIL_RESET_PASSWORD_SUBJECT => '%s Password Reset.',
                    static::MAIL_RESET_PASSWORD_MESSAGE_BODY => 'The reset link in this email is valid for %s. Please <a href="%s%s">Click Here!!</a> to reset your password.'
                ],
            ],
        ];
    }

    public function getNavigation(): array
    {
        return [
            'default' => [
                [
                    'label'     => 'Login',
                    'route'     => 'login',
                    'resource'  => 'login',
                    'privilege' => 'login',
                ],
                [
                    'label'     => 'Logout',
                    'route'     => 'logout',
                    'resource'  => 'logout',
                    'privilege' => 'logout',
                    'order'     => 100,
                ],
                [
                    'label'     => 'Account',
                    'route'     => 'account.read',
                    'resource'  => 'account.read',
                    'privilege' => 'account.read',
                ],
            ],
        ];
    }

    public function getRouteConfig(): array
    {
        return [
            // [
            //     'path'            => '/axleus/admin/user-manager/'
            // ],
            [
                'path'            => '/user-manager/login',
                'name'            => 'login',
                'allowed_methods' => [Http::METHOD_GET, Http::METHOD_POST],
                'middleware'      => [
                    //AuthorizationMiddleware::class,
                    BodyParamsMiddleware::class,
                    Handler\LoginHandler::class,
                ],
            ],
            [
                'path'       => '/user-manager/logout',
                'name'       => 'logout',
                'middleware' => [
                    //AuthorizationMiddleware::class,
                    BodyParamsMiddleware::class,
                    Handler\LogoutHandler::class,
                ],
            ],
            [
                'path'            => '/user-manager/register',
                'name'            => 'register',
                'allowed_methods' => [Http::METHOD_GET, Http::METHOD_POST],
                'middleware'      => [
                    BodyParamsMiddleware::class,
                    MailerMiddleware::class,
                    Handler\RegistrationHandler::class,
                ],
            ],
            [
                'path'            => '/user-manager/reset-password',
                'name'            => 'reset.password',
                'allowed_methods' => [Http::METHOD_GET, Http::METHOD_POST],
                'middleware'      => [
                    BodyParamsMiddleware::class,
                    MailerMiddleware::class,
                    Handler\ResetPasswordHandler::class,
                ],
            ],
            [
                'path'            => '/user-manager/change-password[/{id:\d+}[/{token:[a-zA-Z0-9-]+}]]',
                'name'            => 'change.password',
                'allowed_methods' => [Http::METHOD_GET, Http::METHOD_POST],
                'middleware'      => [
                    BodyParamsMiddleware::class,
                    MailerMiddleware::class,
                    Handler\ChangePasswordHandler::class,
                ],
            ],
            [
                'path'            => '/user-manager/verify[/{id:\d+}[/{token:[a-zA-Z0-9-]+}]]',
                'name'            => 'verify.account',
                'allowed_methods' => [Http::METHOD_GET, Http::METHOD_POST],
                'middleware'      => [
                    BodyParamsMiddleware::class,
                    MailerMiddleware::class,
                    Handler\VerifyAccountHandler::class,
                ],
            ],
            [
                'path'            => '/user-manager/account[/{userId:\d+}]',
                'name'            => 'account.read',
                'allowed_methods' => [Http::METHOD_GET],
                'middleware'      => [
                    Handler\AccountHandler::class,
                ],
            ],
            [
                'path'            => '/user-manager/account[/{userId:\d+}]',
                'name'            => 'account.update',
                'allowed_methods' => [Http::METHOD_POST, Http::METHOD_PUT, Http::METHOD_PATCH],
                'middleware'      => [
                    BodyParamsMiddleware::class,
                    Handler\AccountHandler::class,
                ],

            ],
        ];
    }

    public function getTemplates(): array
    {
        return [
            'paths' => [
                'user-manager'             => [__DIR__ . '/../templates/user-manager'],
                'user-manager-oob-partial' => [__DIR__ . '/../templates/oob-partial'],
                'user-manager-partial'     => [__DIR__ . '/../templates/partial'],
            ],
        ];
    }

    public function getViewHelpers(): array
    {
        return [
            'aliases'   => [
                'authz'         => View\Helper\RbacHelper::class,
                'rbac'          => View\Helper\RbacHelper::class,
                'authorization' => View\Helper\RbacHelper::class,
            ],
            'factories' => [
                View\Helper\RbacHelper::class => View\Helper\RbacHelperFactory::class,
            ],
        ];
    }
}
