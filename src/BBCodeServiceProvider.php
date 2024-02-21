<?php

namespace Khwua\BBCode;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BBCodeServiceProvider extends ServiceProvider
{
    /**
     * Register the bbcode service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('bbcode-parser', function () {
            return new Parser(
                config('bbcode', require_once $this->getConfigPath())
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['bbcode-parser'];
    }

    /**
     * Bootstrap application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([$this->getConfigPath() => config_path('bbcode.php')], 'bbcode-config');

        Blade::directive('bb', function ($bbcode) {
            return "<?php echo app('bbcode-parser')->parse($bbcode) ?>";
        });

        Blade::directive('bbexcept', function ($expression) {
            $expression = explode(',', $expression);
            return "<?php echo app('bbcode-parser')->except($expression[0])->parse($expression[1]) ?>";
        });

        Blade::directive('bbonly', function ($expression) {
            $expression = explode(',', $expression);
            return "<?php echo app('bbcode-parser')->only($expression[0])->parse($expression[1]) ?>";
        });
    }

    /**
     * get package local config file path.
     *
     * @return string
     */
    private function getConfigPath(): string
    {
        return __DIR__ . '/config/bbcode.php';
    }
}
