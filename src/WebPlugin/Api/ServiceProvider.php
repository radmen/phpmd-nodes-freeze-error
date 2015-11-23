<?php

namespace Foo\WebPlugin\Api;

use Foo\WebPlugin\Api\Auth\Guard;
use Foo\WebPlugin\Api\Jwt\TokenFactory;
use Foo\WebPlugin\Api\Middleware\AuthMiddleware;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->registerMiddleware();
        $this->registerTokenFactory();
    }

    protected function registerMiddleware()
    {
        $this->app->bind(AuthMiddleware::class, function () {
            $refreshTtl = $this->app['config']->get('api.jwt.refresh_ttl');

            return new AuthMiddleware(
                $this->app->make(Guard::class),
                $this->app->make(TokenFactory::class),
                $refreshTtl
            );
        });
    }

    protected function registerTokenFactory()
    {
        $this->app->bind(TokenFactory::class, function () {
            $config = $this->app['config']->get('api.jwt');

            return new TokenFactory(
                $config['expiration_ttl'],
                $config['secret_token']
            );
        });
    }
}
