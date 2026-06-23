<?php

namespace ElymodApp\App\Providers;

use ElymodApp\App\Support\Module;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider as Provider;

class ModuleServiceProvider extends Provider
{
    use Module;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMigrations();
        $this->registerConfigs();
    }

    public function boot()
    {
        $this->registerBladeComponents();
        $this->registerMiddlewares();
        $this->loadViewsFrom(__DIR__ . "/../../resources/views", $this->generateViewPrefix());
        $this->registerCommands();
        $this->registerSchedules();
    }

    /**
     * Merge configs
     * @return void
     */
    public function registerConfigs()
    {
        $configPath = __DIR__ . "/../../config";
        $moduleName = $this->getModuleName();

        if (!is_dir($configPath)) {
            return;
        }

        foreach (glob($configPath . "/*.php") as $file) {
            // Config file name
            $key = basename($file, '.php');

            $currentConfig = config($key, []);

            $filePath = include $file;
            $moduleConfig['third-party'][$moduleName] = include $file;

            // Merge configs
            switch ($key) {
                case 'rate_limit':
                    $merged = $this->mergeConfigSmart($moduleConfig, $currentConfig);
                    config()->set($key, $merged);
                    break;
                case 'routes':
                    $merged = $this->mergeConfigSmart($moduleConfig, $currentConfig);
                    config()->set($key, $merged);
                    break;
                case 'module':
                    $merged = $this->mergeConfigSmart($moduleConfig, $currentConfig);
                    config()->set($key, $merged);
                    break;
                case 'menus': // Merge Menus
                    $merged = $this->mergeConfigSmart($filePath, $currentConfig);
                    config()->set($key, $merged);
                    break;
                case 'auth':
                    $merged = $this->mergeConfigSmart($currentConfig, $filePath);
                    config()->set('auth', $merged);
                    break;
                default:
                    $this->mergeConfigFrom($file, $key);
                    break;
            }
        }
    }

    /**
     * Register middlewares
     * @return void
     */
    protected function registerMiddlewares()
    {
        $path = __DIR__ . "/../Http/kernel.php";

        $router = $this->app->make(Router::class);

        if (file_exists($path)) {
            $alias = require $path;

            foreach ($alias as $key => $value) {
                $router->aliasMiddleware($key, $value);
            }
        }
    }

    /**
     * Register the migrations for the Identity module.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . "/../../database/migrations");
    }

    /**
     * Register components
     * @return void
     */
    protected function registerBladeComponents(): void
    {
        $path = __DIR__ . '/../View/Components';

        if (!File::exists($path)) {
            return;
        }

        $files = File::allFiles($path);

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $baseNamespace = "{$this->generateViewPrefix()}\\App\\View\\Components";

            $relativePath = str_replace([$path . '/', '.php'], '', $file->getPathname());

            $class = $baseNamespace . '\\' . str_replace('/', '\\', $relativePath);

            if (!class_exists($class)) {
                continue;
            }

            $reflection = new \ReflectionClass($class);

            if (
                $reflection->isAbstract() ||
                !$reflection->isSubclassOf(\Illuminate\View\Component::class)
            ) {
                continue;
            }

            $alias = str("{$this->generateViewPrefix()}{$reflection->getShortName()}")->kebab()->toString();

            Blade::component($alias, $class);
        }
    }

    /**
     * Register commands
     * @return void
     */
    protected function registerCommands(): void
    {
        $path = __DIR__ . "/../../app/Console/kernel.php";

        if (!file_exists($path)) {
            return;
        }

        $commands = require $path;

        $this->commands($commands);
    }

    protected function registerSchedules(): void
    {
        $path = __DIR__ . '/../../routes/console.php';

        if (!file_exists($path)) {
            return;
        }

        $callback = require $path;

        $callback(
            $this->app->make(Schedule::class)
        );
    }
}
