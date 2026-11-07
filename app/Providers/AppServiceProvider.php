<?php

namespace App\Providers;

use App\Models\BusinessAsset;
use App\Models\RoleAssignment;
use App\Observers\BusinessAssetObserver;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        $this->configureDefaults();
        $this->registerObservers();
        $this->registerViewComposers();
    }

    /**
     * Register model observers.
     */
    protected function registerObservers(): void
    {
        BusinessAsset::observe(BusinessAssetObserver::class);
    }

    /**
     * Register view composers.
     */
    protected function registerViewComposers(): void
    {
        // "My space" section of the sidebar: how many entities of each
        // role-bearing type the authenticated user is responsible for.
        View::composer('layouts.app.sidebar', function (\Illuminate\View\View $view): void {
            $view->with('mySpaceCounts', auth()->check()
                ? RoleAssignment::query()
                    ->where('user_id', auth()->id())
                    ->selectRaw('roleable_type, COUNT(*) as aggregate')
                    ->groupBy('roleable_type')
                    ->pluck('aggregate', 'roleable_type')
                    ->all()
                : []);
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
