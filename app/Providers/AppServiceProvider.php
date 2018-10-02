<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Blade::component('shared._card','card');

        Blade::directive('render',function ($expression){
           $parts = explode(',',$expression,2);
           $component = $parts[0];
           $args=trim($parts[1]??'[]');

           return "<?php echo app('App\Http\ViewComponents\\\\'.{$component},{$args})->toHtml() ?>";
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
