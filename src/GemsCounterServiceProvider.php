<?php

namespace Farajzadeh\GemsCounter;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Farajzadeh\GemsCounter\Contracts\Transaction as TransactionContract;
use Farajzadeh\GemsCounter\Contracts\Gem as GemContract;

class GemsCounterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->offerPublishing();

        $this->registerModelBindings();

    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/gems-counter.php',
            'gems-counter'
        );
    }

    protected function offerPublishing()
    {
        if (! function_exists('config_path')) {
            // function not available and 'publish' not relevant in Lumen
            return;
        }

        $this->publishes([
            __DIR__.'/../config/gems-counter.php' => config_path('gems-counter.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/create_transactions_table.php.stub' => $this->getMigrationFileName('create_transactions_table.php'),
        ], 'migrations');

        $this->publishes([
            __DIR__.'/../database/migrations/create_gems_table.php.stub' => $this->getMigrationFileName('create_gems_table.php'),
        ], 'migrations');
    }

    protected function registerModelBindings()
    {
        $config = $this->app->config['gems-counter.models'];

        if (! $config) {
            return;
        }

        $this->app->bind(GemContract::class, $config['gem']);
        $this->app->bind(TransactionContract::class, $config['transaction']);
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @return string
     */
    protected function getMigrationFileName($migrationFileName): string
    {
        $timestamp = date('Y_m_d_His');

        $filesystem = $this->app->make(Filesystem::class);

        return Collection::make($this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem, $migrationFileName) {
                return $filesystem->glob($path.'*_'.$migrationFileName);
            })
            ->push($this->app->databasePath()."/migrations/{$timestamp}_{$migrationFileName}")
            ->first();
    }
}