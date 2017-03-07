<?php

namespace SlyDeath\NestedCaching;

use Blade;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class NestedCachingServiceProvider extends ServiceProvider
{
    /**
     * Поддерживаемые драйвера кэширования
     *
     * @var array
     */
    protected $supported_drivers = [
        'redis', 'memcached',
    ];

    /**
     * Bootstrap any application services
     *
     * @param Kernel $kernel
     *
     * @throws BadDriverException
     */
    public function boot(Kernel $kernel)
    {
        // Проверка драйвера на поддержку тэгирования кэша
        $this->checkCacheDriverSupport();

        // Установка ПО на очистку кэша
        $this->applyMiddleware($kernel);

        // Добавление директив в Blade
        $this->applyBladeDirectives();

        // Публикация конфигурации
        $this->publishConfig();
    }

    /**
     * Register any application services
     *
     * @return void
     */
    public function register()
    {
        $config_path = __DIR__ . '/../config/nested_caching.php';
        $this->mergeConfigFrom($config_path, 'nested_caching');

        $this->app->singleton(BladeDirectives::class);
    }

    /**
     * Проверка драйвера на поддержку тэгирования кэша
     *
     * @throws BadDriverException
     */
    public function checkCacheDriverSupport()
    {
        if (!in_array(config('cache.default'), $this->supported_drivers, true)) {
            throw new BadDriverException(
                'Your cache driver does not supported. Supported drivers: ' . implode(', ', $this->supported_drivers)
            );
        }
    }

    /**
     * Установка ПО на очистку кэша
     *
     * @param $kernel
     */
    public function applyMiddleware($kernel)
    {
        if (in_array(app('env'), config('nested_caching.expelled_env'), true)) {
            $kernel->pushMiddleware('SlyDeath\NestedCaching\FlushCacheMiddleware');
        }
    }

    /**
     * Добавление директив в Blade
     */
    public function applyBladeDirectives()
    {
        Blade::directive('cache', function ($expression) {
            return "<?php if ( ! app('SlyDeath\NestedCaching\BladeDirectives')->cache({$expression}) ) { ?>";
        });

        Blade::directive('endcache', function () {
            return "<?php } echo app('SlyDeath\NestedCaching\BladeDirectives')->endCache(); ?>";
        });
    }

    /**
     * Публикация конфигурации
     */
    public function publishConfig()
    {
        $config_path = __DIR__ . '/../config/nested_caching.php';
        $publish_path = base_path('config/nested_caching.php');

        $this->publishes([$config_path => $publish_path], 'config');
    }
}
