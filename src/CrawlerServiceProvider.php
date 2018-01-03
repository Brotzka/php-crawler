<?php

namespace Brotzka\PhpCrawler;

use Illuminate\Support\ServiceProvider;

class CrawlerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/routes.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'php-crawler');
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
