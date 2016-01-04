<?php

/**
 * Slim Auth.
 *
 * @link      http://github.com/jeremykendall/slim-auth Canonical source repo
 *
 * @copyright Copyright (c) 2013-2016 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/slim-auth/blob/master/LICENSE MIT
 */
namespace JeremyKendall\Slim\Auth\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Provides Slim Auth services for use with a \Slim\Container.
 *
 * You MUST provide your own AuthAdapter in your instance of \Slim\Container
 * and set it on the AuthenticationService.
 *
 * @see http://pimple.sensiolabs.org/#extending-a-container Pimple - Extending a Container
 */
final class SlimAuthProvider implements ServiceProviderInterface
{
    /**
     * Registers Slim Auth services on the given container.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['auth'] = function ($c) {
            $auth = new \Zend\Authentication\AuthenticationService();
            $auth->setStorage(new \Zend\Authentication\Storage\Session());
            $auth->setAdapter($c->get('authAdapter'));

            return $auth;
        };

        $pimple['acl'] = function ($c) {
            return new \Zend\Permissions\Acl\Acl();
        };

        $pimple['redirectHandler'] = function ($c) {
            return new \JeremyKendall\Slim\Auth\Handlers\RedirectHandler('/login', '/403');
        };

        $pimple['throwHttpExceptionHandler'] = function ($c) {
            return new \JeremyKendall\Slim\Auth\Handlers\ThrowHttpExceptionHandler();
        };

        $pimple['slimAuthAuthorizationMiddleware'] = function ($c) {
            return new \JeremyKendall\Slim\Auth\Middleware\Authorization(
                $c->get('auth'),
                $c->get('acl'),
                $c->get('redirectHandler')
            );
        };
    }
}
