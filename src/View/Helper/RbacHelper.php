<?php

declare(strict_types=1);

namespace Axleus\UserManager\View\Helper;

use Axleus\UserManager\Authz\Rbac;
use Laminas\View\Helper\AbstractHelper;
use Mezzio\Authentication\UserInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RbacHelper extends AbstractHelper
{
    private ServerRequestInterface $request;

    public function __construct(
        private Rbac $rbac
    ) {
    }

    public function __invoke(ServerRequestInterface $request): self
    {
        $this->request = $request;
        return $this;
    }

    public function isGranted(string $routeName, ?UserInterface $userInterface = null): bool
    {
        $this->rbac->setAuthorizeRequestedRoute(false);
        $this->rbac->setRouteName($routeName);

        if (empty($userInterface)) {
            $userInterface = $this->request->getAttribute(UserInterface::class);
        }
        $roles = $userInterface->getRoles();
        foreach ($roles as $role) {
            if ($this->rbac->isGranted($role, $this->request)) {
                return true;
            }
        }
        return false;
    }
}
