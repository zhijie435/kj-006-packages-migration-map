<?php

namespace Shearerline;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Shearerline\Contracts\ShearerlineInterface;
use Shearerline\Events\CourseCompleted;
use Shearerline\Events\StudentEnrolled;
use Shearerline\Listeners\SendEnrollmentNotification;
use Shearerline\Listeners\SendCourseCompletedNotification;
use Shearerline\Models\Course;
use Shearerline\Models\Enrollment;
use Shearerline\Models\Lesson;
use Shearerline\Models\MoqOrder;
use Shearerline\Models\Product;
use Shearerline\Models\Shipment;
use Shearerline\Models\Student;
use Shearerline\Models\Supplier;
use Shearerline\Models\Teacher;
use Shearerline\Policies\CoursePolicy;
use Shearerline\Policies\EnrollmentPolicy;
use Shearerline\Policies\LessonPolicy;
use Shearerline\Policies\MoqOrderPolicy;
use Shearerline\Policies\ProductPolicy;
use Shearerline\Policies\ShipmentPolicy;
use Shearerline\Policies\StudentPolicy;
use Shearerline\Policies\SupplierPolicy;
use Shearerline\Policies\TeacherPolicy;

class ShearerlineServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/shearerline.php',
            'shearerline'
        );

        $this->app->singleton('shearerline', function ($app) {
            return new Shearerline();
        });

        $this->app->bind(ShearerlineInterface::class, Shearerline::class);

        $this->registerCommands();
    }

    public function boot(): void
    {
        $this->registerPolicies();
        $this->registerEvents();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/config/shearerline.php' => config_path('shearerline.php'),
            ], 'shearerline-config');

            if (method_exists($this, 'publishesMigrations')) {
                $this->publishesMigrations([
                    __DIR__ . '/database/migrations' => database_path('migrations'),
                ], 'shearerline-migrations');
            } else {
                $this->publishes([
                    __DIR__ . '/database/migrations' => database_path('migrations'),
                ], 'shearerline-migrations');
            }

            $this->publishes([
                __DIR__ . '/database/seeds' => database_path('seeders'),
            ], 'shearerline-seeds');

            $this->publishes([
                __DIR__ . '/resources/views' => resource_path('views/vendor/shearerline'),
            ], 'shearerline-views');

            $this->publishes([
                __DIR__ . '/resources/js' => resource_path('js/vendor/shearerline'),
            ], 'shearerline-assets');

            $this->publishes([
                __DIR__ . '/resources/lang' => $this->app->langPath('vendor/shearerline'),
            ], 'shearerline-lang');
        }

        $this->registerRoutes();
        $this->registerViews();
        $this->registerMigrations();
    }

    protected function registerEvents(): void
    {
        Event::listen(
            StudentEnrolled::class,
            [SendEnrollmentNotification::class, 'handle']
        );

        Event::listen(
            CourseCompleted::class,
            [SendCourseCompletedNotification::class, 'handle']
        );
    }

    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
    }

    protected function registerViews(): void
    {
        if (config('shearerline.views.enabled', true)) {
            $this->loadViewsFrom(__DIR__ . '/resources/views', 'shearerline');
        }
    }

    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Shearerline\Console\Commands\InstallCommand::class,
            ]);
        }
    }

    protected function registerPolicies(): void
    {
        $policies = [
            Course::class => CoursePolicy::class,
            Lesson::class => LessonPolicy::class,
            Student::class => StudentPolicy::class,
            Teacher::class => TeacherPolicy::class,
            Enrollment::class => EnrollmentPolicy::class,
            Supplier::class => SupplierPolicy::class,
            Product::class => ProductPolicy::class,
            MoqOrder::class => MoqOrderPolicy::class,
            Shipment::class => ShipmentPolicy::class,
        ];

        foreach ($policies as $model => $policy) {
            if (class_exists($model) && class_exists($policy)) {
                Gate::policy($model, $policy);
            }
        }
    }
}
