<?php

namespace Foo\WebPlugin\Users;

use Foo\Domain\Users\Repository\UsersRepositoryInterface;
use Foo\WebPlugin\Users\Repository\DummyUsersRepository;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->app->bind(UsersRepositoryInterface::class, DummyUsersRepository::class);
    }
}
