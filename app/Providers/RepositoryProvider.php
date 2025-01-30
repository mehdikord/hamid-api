<?php

namespace App\Providers;

use App\Interfaces\Auth\AuthInterface;
use App\Interfaces\Profile\ProfileInterface;
use App\Interfaces\Projects\ProjectCategoryInterface;
use App\Interfaces\Projects\ProjectInterface;
use App\Interfaces\Projects\ProjectStatusInterface;
use App\Interfaces\Users\UserInterface;
use App\Repositories\Auth\AuthRepository;
use App\Repositories\Profile\ProfileRepository;
use App\Repositories\Projects\ProjectCategoryRepository;
use App\Repositories\Projects\ProjectRepository;
use App\Repositories\Projects\ProjectStatusRepository;
use App\Repositories\Users\UserRepository;
use Carbon\Laravel\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(AuthInterface::class,AuthRepository::class);

        $this->app->bind(ProfileInterface::class,ProfileRepository::class);

        $this->app->bind(UserInterface::class,UserRepository::class);

        $this->app->bind(ProjectCategoryInterface::class,ProjectCategoryRepository::class);

        $this->app->bind(ProjectStatusInterface::class,ProjectStatusRepository::class);

        $this->app->bind(ProjectInterface::class,ProjectRepository::class);

    }

}



?>
