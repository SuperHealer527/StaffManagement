<?php

namespace App\Providers;

use App\Models\Assignment;
use App\Models\Availability;
use App\Models\Client;
use App\Models\ClientContact;
use App\Models\Event;
use App\Models\Expense;
use App\Models\InboundEmail;
use App\Models\Interview;
use App\Models\InvoiceItem;
use App\Models\Meeting;
use App\Models\PhoneLog;
use App\Models\Qualification;
use App\Models\ReportQuestion;
use App\Models\ReportSubmission;
use App\Models\User;
use App\Observers\AuditObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());

        foreach ([
            User::class, Client::class, ClientContact::class, Meeting::class, Event::class,
            Assignment::class, ReportQuestion::class, ReportSubmission::class, InvoiceItem::class,
            Expense::class, Qualification::class, Availability::class, Interview::class, PhoneLog::class,
            InboundEmail::class,
        ] as $model) {
            $model::observe(AuditObserver::class);
        }
    }
}
