<?php

namespace swatty007\LaravelInlineEditor;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class InlineEditorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->bootBladeDirective();

        $this->publishes([
            __DIR__ . '/config/config.php' => config_path('laravel-inline-editor.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__ . '/config/config.php', 'laravel-inline-editor');

        $this->publishes([
            __DIR__ . '/migrations' => $this->app->databasePath() . '/migrations'
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/assets/js' => config('laravel-inline-editor.paths.js', base_path('resources/assets/js/components')),
            __DIR__ . '/assets/sass' => config('laravel-inline-editor.paths.sass', base_path('resources/assets/sass/plugins')),
        ], 'assets');

        $this->loadViewsFrom(__DIR__ . '/views', 'laravel-inline-editor');

        $this->publishes([
            __DIR__ . '/views' => base_path('resources/views/vendor/laravel-inline-editor')
        ], 'views');

        $this->loadRoutesFrom(__DIR__.'/routes.php');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    protected function bootBladeDirective()
    {
        Blade::directive('inlineEditor', function ($expression) {
            return "<?php if (! swatty007\LaravelInlineEditor\InlineEditor::setUp({$expression})) { ?>";
        });

        Blade::directive('endInlineEditor', function () {
            return "<?php } echo swatty007\LaravelInlineEditor\InlineEditor::tearDown() ?>";
        });
    }
}
