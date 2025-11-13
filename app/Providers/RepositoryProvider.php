<?php

namespace App\Providers;

use App\Interfaces\Activities\ActivityInterface;
use App\Interfaces\Auth\AuthInterface;
use App\Interfaces\Customers\CustomerInterface;
use App\Interfaces\Customers\CustomerSettingsStatusInterface;
use App\Interfaces\Fields\FieldInterface;
use App\Interfaces\ImportMethods\importMethodInterface;
use App\Interfaces\Members\MemberInterface;
use App\Interfaces\Positions\PositionInterface;
use App\Interfaces\Profile\ProfileInterface;
use App\Interfaces\Projects\ProjectCategoryInterface;
use App\Interfaces\Projects\ProjectLevelInterface;
use App\Interfaces\Projects\ProjectInterface;
use App\Interfaces\Projects\ProjectMessageInterface;
use App\Interfaces\Projects\ProjectProductInterface;
use App\Interfaces\Projects\ProjectStatusInterface;
use App\Interfaces\Projects\UserProjectInterface;
use App\Interfaces\Reporting\ReportingInterface;
use App\Interfaces\Tags\TagInterface;
use App\Interfaces\Users\UserCustomerInterface;
use App\Interfaces\Users\UserInterface;
use App\Interfaces\Bot\BotInterface;
use App\Interfaces\StatusMessages\StatusMessageInterface;
use App\Interfaces\Telegram\TelegramInterface;
use App\Interfaces\Whatsapp\WhatsappInterface;
use App\Repositories\Activities\ActivityRepository;
use App\Repositories\Auth\AuthRepository;
use App\Repositories\Customers\CustomerRepository;
use App\Repositories\Customers\CustomerSettingsStatusRepository;
use App\Repositories\Fields\FieldRepository;
use App\Repositories\ImportMethods\ImportMethodRepository;
use App\Repositories\Members\MemberRepository;
use App\Repositories\Positions\PositionRepository;
use App\Repositories\Profile\ProfileRepository;
use App\Repositories\Projects\ProjectCategoryRepository;
use App\Repositories\Projects\ProjectLevelRepository;
use App\Repositories\Projects\ProjectMessageRepository;
use App\Repositories\Projects\ProjectProductRepository;
use App\Repositories\Projects\ProjectRepository;
use App\Repositories\Projects\ProjectStatusRepository;
use App\Repositories\Projects\UserProjectRepository;
use App\Repositories\Reporting\ReportingRepository;
use App\Repositories\Tags\TagRepository;
use App\Repositories\Users\UserCustomerRepository;
use App\Repositories\Users\UserRepository;
use App\Repositories\Bot\BotRepository;
use App\Repositories\StatusMessages\StatusMessageRepository;
use App\Repositories\Telegram\TelegramRepository;
use App\Repositories\Whatsapp\WhatsappRepository;
use Carbon\Laravel\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(ActivityInterface::class,ActivityRepository::class);

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

        $this->app->bind(ReportingInterface::class,ReportingRepository::class);

        $this->app->bind(PositionInterface::class,PositionRepository::class);

        $this->app->bind(UserProjectInterface::class,UserProjectRepository::class);

        $this->app->bind(MemberInterface::class,MemberRepository::class);

        $this->app->bind(ProjectMessageInterface::class,ProjectMessageRepository::class);

        $this->app->bind(ProjectProductInterface::class,ProjectProductRepository::class);

        $this->app->bind(BotInterface::class,BotRepository::class);

        $this->app->bind(TelegramInterface::class,TelegramRepository::class);

        $this->app->bind(WhatsappInterface::class,WhatsappRepository::class);

        $this->app->bind(ProjectLevelInterface::class,ProjectLevelRepository::class);

        $this->app->bind(StatusMessageInterface::class,StatusMessageRepository::class);
    }

}



?>
