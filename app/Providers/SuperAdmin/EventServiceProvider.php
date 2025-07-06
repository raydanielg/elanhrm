<?php

namespace App\Providers\SuperAdmin;

use App\Events\NewCompanyCreatedEvent;
use App\Events\SuperAdmin\NewSupportTicketEvent;
use App\Events\SuperAdmin\EmailVerificationEvent;
use App\Events\SuperAdmin\SupportTicketReplyEvent;
use App\Events\SuperAdmin\SupportTicketRequesterEvent;
use App\Listeners\SuperAdmin\NewSupportTicketListener;
use App\Listeners\SuperAdmin\EmailVerificationListener;
use App\Listeners\SuperAdmin\SupportTicketReplyListener;
use App\Events\SuperAdmin\OfflinePackageChangeRequestEvent;
use App\Listeners\SuperAdmin\SupportTicketRequesterListener;
use App\Events\SuperAdmin\OfflinePackageChangeConfirmationEvent;
use App\Listeners\SuperAdmin\CompanyRegisteredListener;
use App\Listeners\SuperAdmin\OfflinePackageChangeRequestListener;
use App\Listeners\SuperAdmin\OfflinePackageChangeConfirmationListener;
use App\Models\SuperAdmin\FooterMenu;
use App\Models\SuperAdmin\OfflinePlanChange;
use App\Models\SuperAdmin\Package;
use App\Observers\SuperAdmin\FooterMenuObserver;
use App\Observers\SuperAdmin\OfflinePlanChangeObserver;
use App\Observers\SuperAdmin\PackageObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        EmailVerificationEvent::class => [EmailVerificationListener::class],
        NewSupportTicketEvent::class => [NewSupportTicketListener::class],
        OfflinePackageChangeConfirmationEvent::class => [OfflinePackageChangeConfirmationListener::class],
        OfflinePackageChangeRequestEvent::class => [OfflinePackageChangeRequestListener::class],
        SupportTicketReplyEvent::class => [SupportTicketReplyListener::class],
        SupportTicketRequesterEvent::class => [SupportTicketRequesterListener::class],
        NewCompanyCreatedEvent::class => [CompanyRegisteredListener::class],
    ];

    protected $observers = [
        OfflinePlanChange::class => [OfflinePlanChangeObserver::class],
        Package::class => [PackageObserver::class],
        FooterMenu::class => [FooterMenuObserver::class],
    ];

}
