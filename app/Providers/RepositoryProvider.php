<?php

namespace App\Providers;

use App\Interfaces\Auth\AuthInterface;
use App\Interfaces\Customers\CustomerSettingsStatusInterface;
use App\Interfaces\ImportMethods\importMethodInterface;
use App\Interfaces\Profile\ProfileInterface;
use App\Interfaces\Projects\ProjectCategoryInterface;
use App\Interfaces\Projects\ProjectInterface;
use App\Interfaces\Projects\ProjectStatusInterface;
use App\Interfaces\Users\UserInterface;
use App\Repositories\Auth\AuthRepository;
use App\Repositories\Customers\CustomerSettingsStatusRepository;
use App\Repositories\ImportMethods\ImportMethodRepository;
use App\Repositories\Profile\ProfileRepository;
use App\Repositories\Projects\ProjectCategoryRepository;
use App\Repositories\Projects\ProjectRepository;
use App\Repositories\Projects\ProjectStatusRepository;
use App\Repositories\Users\UserRepository;
use Carbon\Laravel\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(AuthInterface::class,AuthRepository::class);

        $this->app->bind(ProfileInterface::class,ProfileRepository::class);

        $this->app->bind(UserInterface::class,UserRepository::class);

        $this->app->bind(ProjectCategoryInterface::class,ProjectCategoryRepository::class);

        $this->app->bind(ProjectStatusInterface::class,ProjectStatusRepository::class);

        $this->app->bind(ProjectInterface::class,ProjectRepository::class);

        $this->app->bind(CustomerSettingsStatusInterface::class,CustomerSettingsStatusRepository::class);

        $this->app->bind(importMethodInterface::class,ImportMethodRepository::class);

    }

}



?>
