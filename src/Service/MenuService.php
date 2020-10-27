<?php

namespace App\Service;

use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MenuService
{
    private $authorizationChecker;
    private $router;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, RouterInterface $router)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->router = $router;
    }

    public function computeGrants(&$e)
    {
        $checker = $this->authorizationChecker;
        $children = &$e['children'];
        $roles = &$e['roles'];

        if ($children) {
            foreach ($children as &$child) {
                $computed = $this->computeGrants($child);
                $roles = array_unique(array_merge($roles, $computed));
            }
        }
        $e['isGranted'] = false;

        foreach ($roles as $role) {
            if ($checker->isGranted($role)) {
                $e['isGranted'] = true;
                continue;
            }
        }

        return $roles;
    }

    public function validateRoutes(&$e)
    {
        $_router = $this->router;
        $children = &$e['children'];

        if ($children) {
            foreach ($children as &$f) {
                $this->validateRoutes($f);
            }
        }
        $path = &$e['path'];
        $path = $path ?? '';

        try {
            $path = $_router->generate($path);

            return;
        } catch (RouteNotFoundException $ex) {
            $path = '#';
        }
    }
}
