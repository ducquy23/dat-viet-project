<?php

namespace App\Providers;

use App\Models\Listing;
use App\Models\Setting;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;

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
        // Share VIP listings và settings với tất cả views
        View::composer('*', function ($view) {
            $vipListings = Listing::active()
                ->whereHas('package', function ($q) {
                    $q->where('code', 'vip');
                })
                ->with(['city', 'district', 'category', 'primaryImage', 'package'])
                ->latest()
                ->take(10)
                ->get();

            $settings = Setting::getCurrent();

            $view->with([
                'vipListings' => $vipListings,
                'settings' => $settings,
            ]);
        });

        // Register helper functions for price formatting
        require_once app_path('Helpers/PriceHelper.php');
    }
}
