<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\Page;
use App\Constants\Status;
use App\Services\PWAService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
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
        $viewShare['settings'] = gs();
        view()->share($viewShare);

        try {
            View::composer('layouts.app', function ($view) {
                $pages = Page::where('status', Status::ACTIVE)
                    ->orderBy('order_column')
                    ->get();
                $menus = Menu::where('status', Status::ACTIVE)
                    ->orderBy('order_column')
                    ->get();
                $view->with('pages', $pages);
                $view->with('menus', $menus);
            });
        } catch (\Exception $e) {
            //throw $th;
        }

        Blade::directive('PWA', function () {
            return (new PWAService)->render();
        });

        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        Model::preventLazyLoading(! app()->isProduction());
    }
}
