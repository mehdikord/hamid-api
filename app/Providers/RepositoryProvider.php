<?php

namespace App\Providers;

use App\Interfaces\Auth\AuthInterface;
use App\Interfaces\Customers\CustomerInterface;
use App\Interfaces\Customers\CustomerSettingsStatusInterface;
use App\Interfaces\Fields\FieldInterface;
use App\Interfaces\ImportMethods\importMethodInterface;
use App\Interfaces\Profile\ProfileInterface;
use App\Interfaces\ProjectLevels\ProjectLevelInterface;
use App\Interfaces\Projects\ProjectCategoryInterface;
use App\Interfaces\Projects\ProjectInterface;
use App\Interfaces\Projects\ProjectStatusInterface;
use App\Interfaces\Reporting\ReportingInterface;
use App\Interfaces\Tags\TagInterface;
use App\Interfaces\Users\UserCustomerInterface;
use App\Interfaces\Users\UserInterface;
use App\Repositories\Auth\AuthRepository;
use App\Repositories\Customers\CustomerRepository;
use App\Repositories\Customers\CustomerSettingsStatusRepository;
use App\Repositories\Fields\FieldRepository;
use App\Repositories\ImportMethods\ImportMethodRepository;
use App\Repositories\Profile\ProfileRepository;
use App\Repositories\ProjectLevels\ProjectLevelRepository;
use App\Repositories\Projects\ProjectCategoryRepository;
use App\Repositories\Projects\ProjectRepository;
use App\Repositories\Projects\ProjectStatusRepository;
use App\Repositories\Reporting\ReportingRepository;
use App\Repositories\Tags\TagRepository;
use App\Repositories\Users\UserCustomerRepository;
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

        $this->app->bind(UserCustomerInterface::class,UserCustomerRepository::class);

        $this->app->bind(CustomerInterface::class,CustomerRepository::class);

        $this->app->bind(FieldInterface::class,FieldRepository::class);

        $this->app->bind(TagInterface::class,TagRepository::class);

        $this->app->bind(ProjectLevelInterface::class,ProjectLevelRepository::class);

        $this->app->bind(ReportingInterface::class,ReportingRepository::class);

    }

}



?>
